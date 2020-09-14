<?php
namespace fruitstudios\linkit\models;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
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
    static public function getName(): string {
        return 'linkitField_Linkit';
    }

    /**
     * @return Type
     */
    static public function getType() {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        return GqlEntityRegistry::createEntity(self::class, new ObjectType([
           'name'   => static::getName(),
           'fields' => self::class . '::getFieldDefinitions',
           'description' => 'This is the interface implemented by all links.',
       ]));
    }

    /**
     * @rejturn array
     */
    public static function getFieldDefinitions(): array {
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
}
