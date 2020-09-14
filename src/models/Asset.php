<?php
namespace fruitstudios\linkit\models;

use Craft;

use craft\gql\interfaces\elements\Asset as CraftAssetGqlInterface;
use fruitstudios\linkit\base\ElementLink;

use craft\elements\Asset as CraftAsset;

class Asset extends ElementLink
{
    // Private
    // =========================================================================

    private $_asset;

    // Static
    // =========================================================================

    public static function elementType()
    {
        return CraftAsset::class;
    }

    public static function elementGqlInterface()
    {
        return CraftAssetGqlInterface::class;
    }

    // Public Methods
    // =========================================================================

    public function getText(): string
    {
        return $this->getCustomOrDefaultText() ?? $this->getAsset()->filename ?? $this->getUrl() ?? '';
    }

    public function getAsset()
    {
        if(is_null($this->_asset))
        {
            $this->_asset = Craft::$app->getAssets()->getAssetById((int) $this->value, $this->ownerElement->siteId ?? null);
        }
        return $this->_asset;
    }
}
