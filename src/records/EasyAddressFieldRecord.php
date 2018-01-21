<?php

namespace studioespresso\easyaddressfield\records;

use craft\db\ActiveRecord;

class EasyAddressFieldRecord extends ActiveRecord
{

	// Props
	// =========================================================================

	public static $tableName = '{{%easyaddressfield}}';

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public static function tableName (): string
	{
		return self::$tableName;
	}
}


