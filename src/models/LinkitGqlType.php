<?php

namespace fruitstudios\linkit\models;

use craft\gql\GqlEntityRegistry;
use craft\gql\types\generators\AssetType;
use fruitstudios\linkit\generators\LinkitType;
use GraphQL\Type\Definition\InterfaceType;
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
     * @return string
     */
    public static function getTypeGenerator(): string
    {
        return LinkitType::class;
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

        LinkitType::generateTypes();

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

    private static function getFieldType($type): string
    {
        $link = $type->getType();
        $relection = new \ReflectionClass($link);
        return $relection->getShortName() . '_Linkit';
    }
}
