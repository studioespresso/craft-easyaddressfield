<?php

namespace studioespresso\easyaddressfield\fields;

use Craft;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;


class EasyAddressFieldField extends Field implements PreviewableFieldInterface
{
	public static function displayName(): string
	{
		return Craft::t('easyaddressfield', 'Easy Address Field');
	}

}