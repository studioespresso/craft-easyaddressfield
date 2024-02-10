<?php

namespace studioespresso\easyaddressfield\assetbundles\easyaddressfield;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EasyAddressFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        $this->sourcePath = "@studioespresso/easyaddressfield/assetbundles/easyaddressfield/dist";
        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/EasyAddressField.css',
            'css/leaflet.css',
        ];

        $this->js = [
            'js/EasyAddressField.js',
            'js/leaflet.js',
        ];


        parent::init();
    }
}
