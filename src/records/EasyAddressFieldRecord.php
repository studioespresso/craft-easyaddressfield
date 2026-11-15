<?php

namespace studioespresso\easyaddressfield\records;

use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * @property int $id
 * @property int $owner
 * @property int $site
 * @property int $field
 * @property string|null $name
 * @property string|null $street
 * @property string|null $street2
 * @property string|null $postalCode
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $latitude
 * @property string|null $longitude
 */
class EasyAddressFieldRecord extends BaseModel
{
    use HasUid;

    protected $table = 'easyaddressfield';

    protected $guarded = ['id'];
}
