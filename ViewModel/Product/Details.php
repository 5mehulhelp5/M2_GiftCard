<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Market\GiftCard\ViewModel\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Market\GiftCard\Model\Attributes;
use Market\GiftCard\Service\Product as GiftCardProductService;

class Details implements ArgumentInterface
{

    /** @var GiftCardProductService  */
    private GiftCardProductService $productService;

    public function __construct(
        GiftCardProductService $productService
    ) {
        $this->productService = $productService;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getIsCustomAmountAllowed(): bool
    {
        $product = $this->productService->getProduct();
        if ($value = $product->getData(Attributes::IS_CUSTOM_Allowed)) {
            return (bool)$value;
        }

        if ($product->getCustomAttribute(Attributes::IS_CUSTOM_Allowed)) {
            return $product->getCustomAttribute(Attributes::IS_CUSTOM_Allowed)->getValue();
        }

        return false;
    }
}
