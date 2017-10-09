<?php

namespace studioespresso\easyaddressfield\web\twig\variables;

use studioespresso\easyaddressfield\Plugin;

class AddressVariable
{

    public function getStaticMap($data, $zoom = 14, $size = '640x640', $directions = false, $target = true)
    {

        $pluginSettings = Plugin::getInstance()->getSettings();
        if (!$pluginSettings->googleApiKey) {
            return false;
        }
        $lat = $data['latitude'];
        $lng = $data['longitude'];
        $baseLink = 'https://maps.googleapis.com/maps/api/staticmap?';
        $params = array(
            'center' => $lat . ',' . $lng,
            'zoom' => $zoom,
            'size' => $size,
            'maptype' => 'roadmap',
            'markers' => 'size:med,color:red|' . $lat . ',' . $lng,
            'key' => $pluginSettings->googleApiKey,
        );
        $image = $baseLink . http_build_query($params);
        return $image;
    }

}