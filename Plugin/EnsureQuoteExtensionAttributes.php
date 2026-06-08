<?php

namespace Market\GiftCard\Plugin;

use Magento\Quote\Api\Data\CartExtensionInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
class EnsureQuoteExtensionAttributes
{

    /** @var CartExtensionInterfaceFactory  */
    private CartExtensionInterfaceFactory $cartExtensionFactory;

    public function __construct(
        CartExtensionInterfaceFactory $cartExtensionFactory
    ) {

        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    public function afterCreate(
        QuoteFactory $subject,
        Quote $quote
    ): Quote {
        $quote->setExtensionAttributes(
            $quote->getExtensionAttributes() ?: $this->cartExtensionFactory->create()
        );
        return $quote;
    }

}
