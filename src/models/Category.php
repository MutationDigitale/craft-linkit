<?php
namespace fruitstudios\linkit\models;

use Craft;

use craft\gql\interfaces\elements\Category as CraftCategoryGql;
use fruitstudios\linkit\Linkit;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Category as CraftCategory;

class Category extends ElementLink
{
    // Private
    // =========================================================================

    private $_category;

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftCategory::class;
    }

    public static function elementGqlType()
    {
        return CraftCategoryGql::class;
    }

    // Public Methods
    // =========================================================================

    public function getCategory()
    {
        if(is_null($this->_category))
        {
            $this->_category = Craft::$app->getCategories()->getCategoryById((int) $this->value, $this->ownerElement->siteId ?? null);
        }
        return $this->_category;
    }
}
