<?php

namespace studioespresso\easyaddressfield\migrations;

use Craft;
use craft\db\Migration;
use studioespresso\easyaddressfield\records\EasyAddressFieldRecord;
use yii\db\Schema;

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
                'city' => $this->string(50),
                'postalCode' => $this->string(50),
                'country' => $this->string(255),
                'latitude'     => $this->decimal(11, 9),
                'longitude'     => $this->decimal(12, 9),

                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid()->notNull(),
        ] );

	    $this->createIndex( null, EasyAddressFieldRecord::$tableName,  ['owner', 'site', 'field'], true  );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
	    $this->dropTable(EasyAddressFieldRecord::$tableName);
    }
}
