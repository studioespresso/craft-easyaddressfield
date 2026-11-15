<?php

namespace studioespresso\easyaddressfield\graphql;

use CraftCms\Cms\Gql\Contracts\GeneratorInterface;
use CraftCms\Cms\Gql\GqlEntityRegistry;
use GraphQL\Type\Definition\Type;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;

class EasyAddressFieldTypeGenerator implements GeneratorInterface
{
    public static function generateTypes(mixed $context = null): array
    {
        return [static::generateType($context)];
    }

    /** @param EasyAddressFieldField $context */
    public static function generateType(mixed $context): EasyAddressFieldType
    {
        $typeName = static::getName($context);

        return GqlEntityRegistry::getOrCreate($typeName, fn () => new EasyAddressFieldType([
            'name' => $typeName,
            'description' => 'This entity has all the EasyAddressField properties',
            'fields' => fn () => [
                'name' => Type::string(),
                'street' => Type::string(),
                'street2' => Type::string(),
                'postalCode' => Type::string(),
                'city' => Type::string(),
                'state' => Type::string(),
                'country' => Type::string(),
                'latitude' => Type::float(),
                'longitude' => Type::float(),
            ],
        ]));
    }

    /** @param EasyAddressFieldField|null $context */
    public static function getName(mixed $context = null): string
    {
        return $context->handle.'_EasyAddressField';
    }
}
