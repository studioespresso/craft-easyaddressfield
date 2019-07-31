<?php

namespace studioespresso\easyaddressfield\fields;

use Cake\Utility\Hash;
use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DataHelper;

class EasyAddressFieldFeedMe extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    public static $name = 'Easy Address Field';
    public static $class = 'studioespresso\easyaddressfield\fields\EasyAddressFieldField';


    // Templates
    // =========================================================================

    public function getMappingTemplate()
    {
        return 'easy-address-field/_feedme';
    }


    // Public Methods
    // =========================================================================

    public function parseField()
    {
        $preppedData = [];

        $fields = Hash::get($this->fieldInfo, 'fields');

        if (!$fields) {
            return null;
        }

        foreach ($fields as $subFieldHandle => $subFieldInfo) {
            $preppedData[$subFieldHandle] = DataHelper::fetchValue($this->feedData, $subFieldInfo);
        }

        // Protect against sending an empty array
        if (!$preppedData) {
            return null;
        }

        return $preppedData;
    }

}
