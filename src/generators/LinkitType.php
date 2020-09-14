<?php

namespace fruitstudios\linkit\generators;

use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;
use fruitstudios\linkit\Linkit;

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
            if ($linkType->elementGqlType() === null) continue;

            $gqlTypes[] = static::generateType($linkType);
        }

        return $gqlTypes;
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        $typeName = self::getFieldType($context);

        $elementGqlType = $context->elementGqlType();

        $fields = TypeManager::prepareFieldDefinitions($elementGqlType::getFieldDefinitions(), $typeName);

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity(
            $typeName,
            new \GraphQL\Type\Definition\ObjectType(
                [
                    'name' => $typeName,
                    'fields' => function () use ($fields) {
                        return $fields;
                    }
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
