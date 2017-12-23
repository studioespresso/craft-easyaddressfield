<?php
/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldSettignsAsset;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;
use studioespresso\easyaddressfield\web\twig\variables\AddressVariable;
use yii\base\Event;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use yii\web\UrlManager;

/**
 * Plugin represents the Easy Address Field plugin.
 *
 * @author Studio Espresso <support@studioespresso.co>
 * @since  1.0
 */
class EasyAddressField extends Plugin {

	/**
	 * @var \studioespresso\easyaddressfield\Plugin Plugin instance
	 */
	public static $plugin;

	/**
	 * @var bool
	 */
	public $hasCpSettings = true;

	// Public Methods
	// =========================================================================
	/**
	 * @inheritdoc
	 */
	public function init() {
		self::$plugin = $this;

		// Register our fields
		Event::on(
			Fields::className(),
			Fields::EVENT_REGISTER_FIELD_TYPES,
			function ( RegisterComponentTypesEvent $event ) {
				$event->types[] = EasyAddressFieldField::class;

			}
		);

		// Register our twig functions
		Event::on( CraftVariable::class, CraftVariable::EVENT_INIT, function ( Event $event ) {
			$variable = $event->sender;
			$variable->set( 'address', AddressVariable::class );
		} );
	}

	/**
	 * Creates and returns the model used to store the plugin’s settings.
	 *
	 * @return \craft\base\Model|null
	 */
	protected function createSettingsModel() {
		return new EasyAddressFieldSettingsModel();
	}

	/**
	 * @return string
	 * @throws \yii\base\Exception
	 * @throws \Twig_Error_Loader
	 * @throws \RuntimeException
	 */
	protected function settingsHtml(): string {
		$url = Craft::$app->assetManager->getPublishedUrl('@studioespresso/easyaddressfield/assets', true);
		$styleOptions = [
			'standard'  => Craft::t( "easyaddressfield", "Standard" ),
			'silver'    => Craft::t( "easyaddressfield", 'Silver' ),
			'retro'     => Craft::t( "easyaddressfield", 'Retro' ),
			'dark'      => Craft::t( "easyaddressfield", 'Dark' ),
			'night'     => Craft::t( "easyaddressfield", 'Night' ),
			'aubergine' => Craft::t( "easyaddressfield", 'Aubergine' ),
		];

		return Craft::$app->getView()->renderTemplate(
			'easyaddressfield/_settings',
			[
				'settings'     => $this->getSettings(),
				'styleOptions' => $styleOptions,
				'url' => $url,
			]
		);
	}
}
