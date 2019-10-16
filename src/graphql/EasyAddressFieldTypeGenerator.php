<?php

namespace studioespresso\easyaddressfield\graphql;


use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use GraphQL\Type\Definition\Type;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;

class EasyAddressFieldTypeGenerator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        /** @var EasyAddressFieldField $context */
        $typeName = self::getName($context);

        $addressProperties = [
            'name' => Type::string(),
            'street' => Type::string(),
            'street2' => Type::string(),
            'postalCode' => Type::string(),
            'city' => Type::string(),
            'state' => Type::string(),
            'country' => Type::string(),
            'latitude' => Type::float(),
            'longitude' => Type::float(),
        ];


        $addressProperty = GqlEntityRegistry::getEntity($typeName)
            ?: GqlEntityRegistry::createEntity($typeName, new EasyAddressFieldResolver([
                'name' => $typeName,
                'description' => 'This entity has all the EasyAddressField properties',
                'fields' => function () use ($addressProperties) {
                    return $addressProperties;
                },
            ]));

        TypeLoader::registerType($typeName, function () use ($addressProperty) {
            return $addressProperty;
        });
        return [$addressProperty];
    }

    /**
     * @inheritdoc
     */
    public static function getName($context = null): string
    {
        /** @var EasyAddressFieldField $context */
        return $context->handle . '_EasyAddressField';
    }
}