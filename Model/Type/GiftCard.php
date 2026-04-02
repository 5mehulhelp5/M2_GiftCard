<?php

namespace Market\GiftCard\Model\Type;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Framework\DataObject;
use Market\GiftCard\Model\Attributes;

class GiftCard extends Virtual
{
    public const TYPE_CODE = 'giftcard';

    public function isSalable($product): bool
    {
        $salable = (int)$product->getStatus() === Status::STATUS_ENABLED;

        $product->setData('is_salable', $salable);
        return (bool)(int)$salable;
    }

    public function prepareForCartAdvanced(DataObject $buyRequest, $product, $processMode = null): array|string
    {
        $products = parent::prepareForCartAdvanced($buyRequest, $product, $processMode);

        if (is_string($products)) {
            return $products;
        }

        foreach ($products as $item) {
            $this->assignProductOptions($buyRequest, $item);
        }

        return $products;
    }

    /**
     * @param DataObject $buyRequest
     * @param mixed $product
     * @return void
     */
    private function assignProductOptions(DataObject $buyRequest, mixed $product): void
    {
        foreach (Attributes::OPTION_LIST as $option) {
            if (!$buyRequest->getData($option)) {
                continue;
            }
            $product->addCustomOption($option, $buyRequest->getData($option));
        }
    }
}
