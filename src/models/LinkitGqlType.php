<?php

namespace fruitstudios\linkit\models;

use craft\base\Field;
use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\interfaces\elements\GlobalSet as GlobalSetInterface;
use craft\gql\TypeManager;
use craft\gql\types\elements\GlobalSet;
use fruitstudios\linkit\base\ElementLink;
use fruitstudios\linkit\base\LinkInterface;
use fruitstudios\linkit\Linkit;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class LinkitGqlType
 * @package fruitstudios\linkit\models
 */
class LinkitGqlType
{
    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'linkitField_Linkit';
    }

    /**
     * @return Type
     */
    public static function getType()
    {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(
            self::class,
            new InterfaceType(
                [
                    'name' => static::getName(),
                    'fields' => self::class . '::getFieldDefinitions',
                    'description' => 'This is the interface implemented by all links.',
                    'resolveType' => self::class . '::resolveTypeName',
                ]
            )
        );

        self::generateTypes();

        return $type;
    }

    /**
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'text' => [
                'name' => 'text',
                'type' => Type::string()
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string()
            ],
            'openNewWindow' => [
                'name' => 'openNewWindow',
                'type' => Type::boolean(),
                'resolve' => function ($link) {
                    return $link->target;
                }
            ]
        ];
    }

    public static function resolveTypeName($type)
    {
        return self::getFieldType($type);
    }

    public static function generateTypes()
    {
        $linkTypes = Linkit::$plugin->service->getAvailableLinkTypes();
        $gqlTypes = [];

        foreach ($linkTypes as $linkType) {
            $gqlTypes[] = static::generateType($linkType);
        }

        return $gqlTypes;
    }

    public static function generateType(LinkInterface $linkType)
    {
        $typeName = self::getFieldType($linkType);

        $elementType = $linkType->elementType();

        if ($elementType === null) {
            return null;
        }

        $elementGqlType = $linkType->elementGqlType();

        $contentFields = $elementType::getFields();
        $contentFieldGqlTypes = [];

        /** @var Field $contentField */
        foreach ($contentFields as $contentField) {
            $contentFieldGqlTypes[$contentField->handle] = $contentField->getContentGqlType();
        }

        $fields = TypeManager::prepareFieldDefinitions(
            array_merge($elementGqlType::getFieldDefinitions(), $contentFieldGqlTypes),
            $typeName
        );

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity(
            $typeName,
            new ObjectType(
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
