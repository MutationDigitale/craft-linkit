<?php
namespace fruitstudios\linkit\models;

use Craft;

use craft\gql\interfaces\elements\Entry as CraftEntryGql;
use fruitstudios\linkit\Linkit;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Entry as CraftEntry;

class Entry extends ElementLink
{
    // Private
    // =========================================================================

    private $_entry;

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftEntry::class;
    }

    public static function elementGqlType()
    {
        return CraftEntryGql::class;
    }

    // Public Methods
    // =========================================================================

    public function getEntry()
    {
        if(is_null($this->_entry))
        {
            $this->_entry = Craft::$app->getEntries()->getEntryById((int) $this->value, $this->ownerElement->siteId ?? null);
        }
        return $this->_entry;
    }
}
