<?php

namespace studioespresso\easyaddressfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldAsset;
use studioespresso\easyaddressfield\EasyAddressField;


class EasyAddressFieldField extends Field implements PreviewableFieldInterface
{
	public static function displayName(): string
	{
		return Craft::t('easyaddressfield', 'Easy Address Field');
	}


	public function getInputHtml( $value,  ElementInterface $element = null ): string {
		// Register our asset bundle
		Craft::$app->getView()->registerAssetBundle(EasyAddressFieldAsset::class);

		// Get our id and namespace
		$id = Craft::$app->getView()->formatInputId($this->handle);
		$namespacedId = Craft::$app->getView()->namespaceInputId($id);

		$pluginSettings = EasyAddressField::getInstance()->getSettings();
		$fieldSettings = $this->getSettings();

		return $this->renderFormFields($value);
	}

	protected function renderFormFields(AddressModel $value = null)
	{
		// Get our id and namespace
		$id = Craft::$app->getView()->formatInputId($this->handle);
		$namespacedId = Craft::$app->getView()->namespaceInputId($id);

		$fieldSettings = $this->getSettings();
		$pluginSettings = EasyAddressField::getInstance()->getSettings();

		$fieldLabels = null;
		$addressFields = null;

		return Craft::$app->getView()->renderTemplate(
			'easyaddressfield/_field/_EasyAddressFieldField',
			[
				'name' => $this->handle,
				'value' => $value,
				'field' => $this,
				'id' => $id,
				'namespacedId' => $namespacedId,
				'fieldSettings' => $fieldSettings,
				'pluginSettings' => $pluginSettings,
			]
		);
	}

}