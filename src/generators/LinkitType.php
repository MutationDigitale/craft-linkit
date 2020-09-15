<?php

namespace fruitstudios\linkit\generators;

use craft\gql\base\GeneratorInterface;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use fruitstudios\linkit\Linkit;
use fruitstudios\linkit\models\LinkitGqlType;

class LinkitType implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        $linkTypes = Linkit::$plugin->service->getAvailableLinkTypes();
        $gqlTypes = [];

        foreach ($linkTypes as $linkType) {
            $gqlTypes[] = static::generateType($linkType);
        }

        return $gqlTypes;
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context)
    {
        $typeName = self::getFieldType($context);
        $elementGqlInterface = $context->elementGqlInterface();
        $elementGqlArguments = $context->elementGqlArguments();
        $fieldName = $context->getTypeHandle();

        $commonFields = LinkitGqlType::getFieldDefinitions();
        $fields = $commonFields;

        if ($elementGqlInterface !== null && $elementGqlArguments !== null) {
            $elementFields = array($fieldName => array(
                'name' => $fieldName,
                'type' => $elementGqlInterface::getType(),
                'args' => $elementGqlArguments::getArguments(),
            ));
            $fields = array_merge(
                $commonFields,
                $elementFields
            );
        }

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity(
            $typeName,
            new \GraphQL\Type\Definition\ObjectType(
                [
                    'name' => $typeName,
                    'fields' => $fields,
                    'interfaces' => [LinkitGqlType::getType()]
                ]
            )
        );
    }

    private static function getFieldType($type): string
    {
        $link = $type->getType();
        $relection = new \ReflectionClass($link);
        return $relection->getShortName() . '_Linkit';
    }
}
