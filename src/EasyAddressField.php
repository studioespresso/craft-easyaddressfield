<?php
/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use craft\events\RegisterComponentTypesEvent;
use craft\base\Plugin;
use craft\services\Fields;
use yii\base\Event;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;

/**
 * Plugin represents the Easy Address Field plugin.
 *
 * @author Studio Espresso <support@studioespresso.co>
 * @since  1.0
 */
class EasyAddressField extends Plugin {

	public static $plugin;


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
	}
}
