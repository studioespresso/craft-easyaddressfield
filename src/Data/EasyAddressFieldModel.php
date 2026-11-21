<?php

namespace studioespresso\easyaddressfield\Data;

use ArrayAccess;
use CraftCms\Cms\Support\Facades\Deprecator;
use CraftCms\Cms\Support\Json;
use Illuminate\Contracts\Support\Arrayable;
use studioespresso\easyaddressfield\Services\CountriesService;
use Twig\Markup;

/**
 * The value of an Easy Address Field
 *
 * @implements Arrayable<string, mixed>
 * @implements ArrayAccess<string, mixed>
 */
class EasyAddressFieldModel implements Arrayable, ArrayAccess
{
    /** Address ID */
    public ?int $id = null;

    /** Element ID */
    public ?int $owner = null;

    /** Site ID */
    public ?int $site = null;

    /** Field ID */
    public ?int $field = null;

    public ?string $name = null;

    public ?string $street = null;

    public ?string $street2 = null;

    public ?string $postalCode = null;

    public ?string $city = null;

    public ?string $state = null;

    public ?string $country = null;

    public ?string $latitude = null;

    public ?string $longitude = null;

    private const ATTRIBUTES = ['id', 'owner', 'site', 'field', 'name', 'street', 'street2', 'postalCode', 'city', 'state', 'country', 'latitude', 'longitude'];

    /**
     * @param  self|array<string, mixed>|string|null  $attributes
     */
    public function __construct(self|array|string|null $attributes = [])
    {
        if ($attributes instanceof self) {
            $attributes = $attributes->toArray();
        } elseif (! is_array($attributes)) {
            $attributes = Json::decodeIfJson($attributes);
            $attributes = is_array($attributes) ? $attributes : [];
        }

        foreach (self::ATTRIBUTES as $key) {
            if (array_key_exists($key, $attributes)) {
                $this[$key] = $attributes[$key];
            }
        }
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * Fallback for older installs of StatikAddress
     */
    public function lat(): ?string
    {
        Deprecator::log(self::class.'lat', "The 'lat' method has been renamed to 'latitude'.");

        return $this->latitude;
    }

    /**
     * Fallback for older installs of StatikAddress
     */
    public function long(): ?string
    {
        Deprecator::log(self::class.'long', "The 'long' method has been renamed to 'longitude'.");

        return $this->longitude;
    }

    public function getCountryName(?string $locale = null): ?string
    {
        if (! $this->country) {
            return null;
        }

        return app(CountriesService::class)->getCountryNameByAlpha2($this->country, $locale ?? app()->getLocale());
    }

    public function getDirectionsUrl(bool $currentLocation = false): string
    {
        $url = $currentLocation ? 'https://www.google.com/maps/dir/current+location/' : 'https://www.google.com/maps/dir//';

        return $url.str_replace(' ', '+', $this->toString(','));
    }

    public function toString(string $glue = '+'): string
    {
        return $this->join(['street', 'street2', 'postalCode', 'city', 'country'], $glue);
    }

    public function modelToString(string $glue = '+'): string
    {
        return $this->join(['name', 'street', 'street2', 'postalCode', 'city', 'state', 'country'], $glue);
    }

    public function formatted(bool $includeCountry = false): Markup
    {
        $lines = [
            $this->name,
            $this->street,
            $this->street2,
            trim($this->postalCode.' '.$this->city),
            $this->state,
        ];
        $html = implode('', array_map(fn (string $line) => e($line).'<br>', array_filter($lines, fn ($line) => (string) $line !== '')));

        if ($includeCountry) {
            $html .= e($this->getCountryName());
        }

        return new Markup($html, 'UTF-8');
    }

    /**
     * Whether the address has no content. A country on its own counts as empty, since the input always posts one.
     *
     * @param  list<string>  $params
     */
    public function isEmpty(array $params = []): bool
    {
        $keys = $params ?: ['name', 'street', 'street2', 'postalCode', 'state', 'country'];
        $values = array_filter(array_intersect_key($this->toArray(), array_flip($keys)));

        return $values === [] || array_keys($values) === ['country'];
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_combine(self::ATTRIBUTES, array_map(fn (string $key) => $this->$key, self::ATTRIBUTES));
    }

    public function offsetExists(mixed $offset): bool
    {
        return in_array($offset, self::ATTRIBUTES, true);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->$offset : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! $this->offsetExists($offset)) {
            return;
        }

        $value = $value === '' || $value === null ? null : $value;
        $this->$offset = in_array($offset, ['id', 'owner', 'site', 'field'], true)
            ? ($value === null ? null : (int) $value)
            : ($value === null ? null : (string) $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->offsetSet($offset, null);
    }

    /** @param list<string> $keys */
    private function join(array $keys, string $glue): string
    {
        $parts = [];
        foreach ($keys as $key) {
            if (! empty($this->$key)) {
                $parts[] = $key === 'country' ? $this->getCountryName() : $this->$key;
            }
        }

        return implode($glue, $parts);
    }
}
