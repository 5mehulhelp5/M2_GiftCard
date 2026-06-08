<?php

namespace Market\GiftCard\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartSearchResultsInterface;
use Market\GiftCard\Model\ResourceModel\GiftCardQuote;

class AssignGiftCardIdToQuote
{
    /** @var CartExtensionInterfaceFactory  */
    private CartExtensionInterfaceFactory $cartExtensionFactory;

    /** @var GiftCardQuote  */
    private GiftCardQuote $giftCardQuote;

    public function __construct(
        CartExtensionInterfaceFactory $cartExtensionFactory,
        GiftCardQuote $giftCardQuote
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->giftCardQuote = $giftCardQuote;
    }

    /**
     * @throws LocalizedException
     */
    public function afterGet(
        CartRepositoryInterface $subject,
        CartInterface $cart,
    ): CartInterface {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }
    /**
     * @throws LocalizedException
     */
    public function afterGetForCustomer(
        CartRepositoryInterface $subject,
        CartInterface $cart,
    ): CartInterface {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @throws LocalizedException
     */
    public function afterGetForActiveCustomer(
        CartRepositoryInterface $subject,
        CartInterface $cart,
    ): CartInterface {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @throws LocalizedException
     */
    public function afterGetActive(
        CartRepositoryInterface $subject,
        CartInterface $cart,
    ): CartInterface {
        $this->loadExtensionAttributes($cart);
        return $cart;
    }

    /**
     * @throws LocalizedException
     */
    public function afterGetList(
        CartRepositoryInterface $subject,
        CartSearchResultsInterface $results,
    ): CartSearchResultsInterface {
        foreach ($results->getItems() as $cart) {
            $this->loadExtensionAttributes($cart);
        }
        return $results;
    }

    public function afterSave(
        CartRepositoryInterface $subject,
        $result,
        CartInterface $cart
    ) {
        if (!$cart->getId()) {
            return $result;
        }
        if ($cart->getExtensionAttributes()->getGiftCardId()) {
            $this->giftCardQuote->add(
                (int)$cart->getId(),
                (int)$cart->getExtensionAttributes()->getGiftCardId()
            );
        } else {
            $this->giftCardQuote->add((int)$cart->getId(), null);
        }
        return $result;
    }

    /**
     * @throws LocalizedException
     */
    private function loadExtensionAttributes(CartInterface $cart): void
    {
        $extensionAttributes = $cart->getExtensionAttributes() ?: $this->cartExtensionFactory->create();

        if ($extensionAttributes->getGiftCardId()) {
            return;
        }

        $giftCardId = $this->giftCardQuote->get((int)$cart->getId());
        $extensionAttributes->setGiftCardId($giftCardId);

        $cart->setExtensionAttributes($extensionAttributes);

    }
}
