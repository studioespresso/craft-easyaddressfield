<?php

namespace studioespresso\easyaddressfield\models;

use Craft;
use craft\helpers\Template;
use craft\web\View;
use studioespresso\easyaddressfield\EasyAddressField;
use yii\base\Model;

/**
 * Class EasyAddressFieldModel
 * @package studioespresso\easyaddressfield\models
 */
class EasyAddressFieldModel extends Model
{

    /**
     * EasyAddressFieldModel constructor.
     * @param array $attributes
     * @param array $config
     */
    public function __construct($attributes = [], array $config = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this[$key] = $value;
            }
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'name',
                    'street',
                    'street2',
                    'postalCode',
                    'city',
                    'state',
                    'country',
                    'latitude',
                    'longitude',
                ],
                'safe',
            ],
        ];
    }

    /**
     *  Address ID
     *
     * @var
     */
    public $id;

    /**
     * Element ID
     *
     * @var
     */
    public $owner;

    /**
     * Site ID
     *
     * @var
     */
    public $site;

    /**
     *  Field ID
     *
     * @var
     */
    public $field;

    /**
     *  The name of the address
     *
     * @var
     */
    public $name;

    /**
     * Latitude
     *
     * @var string
     */
    public $latitude;

    /**
     * Longitude
     *
     * @var string
     */
    public $longitude;

    /**
     * The first line of the street block.
     *
     * @var string
     */
    public $street;

    /**
     * The second line of the street block.
     *
     * @var string
     */
    public $street2;

    /**
     * The postal code.
     *
     * @var string
     */
    public $postalCode;

    /**
     * @var
     */
    public $city;

    /**
     * @var
     */
    public $state;

    /**
     * @var
     */
    public $country;

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @param $locale
     * @return string
     */
    public function getCountryName($locale = null): string
    {
        if (!$locale) {
            $locale = Craft::$app->getLocale();
        }
        if ($this->country) {
            $name = EasyAddressField::getInstance()->countries->getCountryNameByAlpha2($this->country, $locale);
            return $name;
        }
    }

    /**
     * @param string $glue
     * @return string
     */
    public function toString($glue = '+'): string
    {
        $data = array();
        if (!empty($this->street)) {
            $data['street'] = $this->street;
        }
        if (!empty($this->street2)) {
            $data['street2'] = $this->street2;
        }
        if (!empty($this->postalCode)) {
            $data['postalCode'] = $this->postalCode;
        }
        if (!empty($this->city)) {
            $data['city'] = $this->city;
        }
        if (!empty($this->country)) {
            $data['country'] = $this->countryName;
        }

        return implode($glue, $data);
    }

    public function modelToString($glue = '+'): string
    {
        $data = array();
        if (!empty($this->name)) {
            $data['name'] = $this->name;
        }
        if (!empty($this->street)) {
            $data['street'] = $this->street;
        }
        if (!empty($this->street2)) {
            $data['street2'] = $this->street2;
        }
        if (!empty($this->postalCode)) {
            $data['postalCode'] = $this->postalCode;
        }
        if (!empty($this->city)) {
            $data['city'] = $this->city;
        }
        if (!empty($this->state)) {
            $data['state'] = $this->state;
        }
        if (!empty($this->country)) {
            $data['country'] = $this->countryName;
        }

        return implode($glue, $data);
    }

    public function formatted($includeCountry = false)
    {
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $template = $view->renderTemplate('easy-address-field/_formatted', [
            'address' => $this,
            'includeCountry' => $includeCountry
        ]);

        $view->setTemplateMode($oldTemplateMode);
        return Template::raw($template);

    }


    public function isEmpty()
    {

        $values = array_filter($this->toArray());
        if (count($values) == 1 && isset($values['country'])) {
            return true;
        } else {
            return false;
        }
    }

}