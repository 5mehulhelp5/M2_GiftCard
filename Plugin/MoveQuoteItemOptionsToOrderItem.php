<?php

namespace Market\GiftCard\Plugin;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\Option;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory as QuoteItemOptionCollectionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Market\GiftCard\Model\Attributes;
use Market\GiftCard\Model\Type\GiftCard;

class MoveQuoteItemOptionsToOrderItem
{

    private QuoteItemOptionCollectionFactory $collectionFactory;

    public function __construct(
        QuoteItemOptionCollectionFactory $collectionFactory,
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function afterConvert(ToOrderItem $subject, OrderItemInterface $orderItem, CartItemInterface $cartItem): OrderItemInterface
    {

        if ($cartItem->getProductType() !== GiftCard::TYPE_CODE) {
            return $orderItem;
        }

        $orderItemOptions = $orderItem->getProductOptions();

        $quoteItemOptions = $this->collectionFactory->create()
            ->addFieldToFilter('item_id', $cartItem->getId());

        /** @var Option $option */
        foreach ($quoteItemOptions as $option) {
            if (!in_array($option->getData('code'), Attributes::OPTION_LIST, true)) {
                continue;
            }

            $orderItemOptions[$option->getData('code')] = $option->getData('value');
        }

        $orderItem->setProductOptions($orderItemOptions);

        return $orderItem;
    }

}
