<?php
namespace fruitstudios\linkit;

use craft\events\RegisterGqlTypesEvent;
use craft\services\Gql;
use fruitstudios\linkit\fields\LinkitField;
use fruitstudios\linkit\models\LinkitGqlType;
use fruitstudios\linkit\services\LinkitService;

use Craft;
use craft\base\Plugin;
use yii\base\Event;

use craft\events\RegisterComponentTypesEvent;

use craft\services\Fields;

use craft\commerce\Plugin as CommercePlugin;

class Linkit extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;
    public static $commerceInstalled;


    // Public Methods
    // =========================================================================

    public $schemaVersion = '1.0.8';

    public function init()
    {
        parent::init();

        self::$plugin = $this;
        self::$commerceInstalled = class_exists(CommercePlugin::class);

        $this->setComponents([
            'service' => LinkitService::class,
        ]);

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = LinkitField::class;
            }
        );

        Craft::info(
            Craft::t('linkit', '{name} plugin loaded', [
                'name' => $this->name
            ]),
            __METHOD__
        );

        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_TYPES,
            function (RegisterGqlTypesEvent $event) {
                $event->types[] = LinkitGqlType::class;
            }
        );
    }
}
