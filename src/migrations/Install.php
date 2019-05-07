<?php

namespace studioespresso\easyaddressfield\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\helpers\Json;
use craft\services\Fields;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\records\EasyAddressFieldRecord;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            EasyAddressFieldRecord::$tableName, [
            'id' => $this->primaryKey(),
            'owner' => $this->integer()->notNull(),
            'site' => $this->integer()->notNull(),
            'field' => $this->integer()->notNull(),

            'name' => $this->string(255),
            'street' => $this->string(100),
            'street2' => $this->string(100),
            'postalCode' => $this->string(50),
            'city' => $this->string(50),
            'state' => $this->string(50),
            'country' => $this->string(255),
            'latitude' => $this->decimal(11, 9),
            'longitude' => $this->decimal(12, 9),

            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()->notNull(),
        ]);

        $this->createIndex(null, EasyAddressFieldRecord::$tableName, ['owner', 'site', 'field'], true);
        $this->statikAddressUpgrade();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(EasyAddressFieldRecord::$tableName);
    }

    public function statikAddressUpgrade()
    {

        $statikAddressFields = (new Query())
            ->select(['handle', 'id'])
            ->from(['{{%fields}}'])
            ->where([
                'and',
                ['like', 'context', 'global'],
                ['in', 'type', ['StatikAddress']]
            ])
            ->all();
        foreach ($statikAddressFields as $field) {
            $addressEntries = (new Query())
                ->select(['elementId', 'siteId', 'field_' . $field['handle']])
                ->from(['{{%content}}'])
                ->where(['IS NOT', 'field_' . $field['handle'], null])
                ->all();
            foreach ($addressEntries as $content) {
                $value = Json::decode($content['field_' . $field['handle']]);

                $record = new EasyAddressFieldRecord();

                $record->owner = $content['elementId'];
                $record->site = $content['siteId'];
                $record->field = $field['id'];

                $record->name = $value['name'];
                $record->street = $value['street'];
                $record->street2 = $value['street2'];
                $record->postalCode = $value['postalCode'];
                $record->city = $value['city'];
                $record->state = $value['region'];
                $record->country = $value['country'];
                $record->latitude = $value['lat'];
                $record->longitude = $value['long'];

                $record->save();
            }
        }

        // Get the field data from the project config
        $projectConfig = Craft::$app->getProjectConfig();
        $projectConfig->muteEvents = true;

        $fieldConfigs = $projectConfig->get(Fields::CONFIG_FIELDS_KEY) ?? [];
        $fieldConfigsToMigrate = [];
        foreach ($fieldConfigs as $fieldUid => $fieldConfig) {
            if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'StatikAddress') {
                $fieldConfigsToMigrate[$fieldUid] = [
                    'configPath' => Fields::CONFIG_FIELDS_KEY . '.' . $fieldUid,
                    'config' => $fieldConfig
                ];
            }
        }

        // Migrate Fields
        if ($fieldConfigsToMigrate) {
            foreach ($fieldConfigsToMigrate as $fieldUid => $fieldConfig) {
                $type = EasyAddressFieldField::class;
                $settings = $this->_migrateFieldSettings($fieldConfig['config']['settings'] ?? false);
                $fieldConfig['config']['type'] = $type;
                $fieldConfig['config']['settings'] = $settings;
                $this->update('{{%fields}}', [
                    'type' => $type,
                    'settings' => Json::encode($settings),
                ], ['uid' => $fieldUid]);
                if ($fieldConfig['configPath']) {
                    $projectConfig->set($fieldConfig['configPath'], $fieldConfig['config']);
                }
            }
        }
        $projectConfig->muteEvents = false;
    }

    private function _migrateFieldSettings($oldSettings)
    {
        if (!$oldSettings) {
            return null;
        }

        $easyAddressField = new EasyAddressFieldField();
        $newSettings = $easyAddressField->getSettings();
        $newSettings['defaultCountry'] = $oldSettings['defaultCountry'];
        $newSettings['showCoordinates'] = true;
        $newSettings['geoCode'] = true;
        $newSettings['enabledFields'] = array(
            'name' => false,
            'street' => true,
            'street2' => true,
            'postalCode' => true,
            'city' => true,
            'state' => false,
            'country' => true,
        );

        if ($oldSettings['showName']) {
            $newSettings['enabledFields']['name'] = true;
        }

        return $newSettings;
    }
}
