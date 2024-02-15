<?php

namespace studioespresso\easyaddressfield\web\twig\variables;

use studioespresso\easyaddressfield\EasyAddressField;

class AddressVariable
{
    public function countries(): array
    {
        return EasyAddressField::getInstance()->countries->getCountriesAsArray();
    }
}
