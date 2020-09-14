<?php

namespace fruitstudios\linkit\generators;

use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;
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
            $typeName = self::getFieldType($context);

            $elementGqlInterface = $context->elementGqlInterface();

            if ($linkType->$elementGqlInterface() === null) continue;

            $fields = TypeManager::prepareFieldDefinitions($elementGqlInterface::getFieldDefinitions(), $typeName);

            $type = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity(
                $typeName,
                new \GraphQL\Type\Definition\ObjectType(
                    [
                        'name' => $typeName,
                        'fields' => $fields,
                        'interfaces' => [LinkitGqlType::getInterfaceType()]
                    ]
                )
            );

            $gqlTypes[] = $type;
        }

        return $gqlTypes;
    }

    private static function getFieldType($type): string
    {
        $link = $type->getType();
        $relection = new \ReflectionClass($link);
        return $relection->getShortName() . '_Linkit';
    }
}
