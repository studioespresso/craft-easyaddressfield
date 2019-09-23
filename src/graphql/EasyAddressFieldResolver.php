<?php

namespace studioespresso\easyaddressfield\graphql;

use craft\gql\base\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class EasyAddressFieldResolver extends ObjectType
{

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        $fieldName = $resolveInfo->fieldName;

        return $source->$fieldName;
    }
}