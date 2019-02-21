<?php

namespace studioespresso\easyaddressfield\models;

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
            $data['country'] = $this->country;
        }

        return implode($glue, $data);
    }

}