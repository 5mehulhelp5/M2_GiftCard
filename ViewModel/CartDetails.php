<?php

namespace Market\GiftCard\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote\Item;
use Market\GiftCard\Model\Attributes;

class CartDetails implements ArgumentInterface
{

    public function getCardDetails(Item $item): array
    {
        $codes = [
            (string) __('Recipient Name') => Attributes::OPTION_RECIPIENT_NAME,
            (string) __('Recipient Email') => Attributes::OPTION_RECIPIENT_EMAIL
        ];

        $output = array_map(function ($value) use ($item) {
            return $item->getOptionByCode($value)
                ? $item->getOptionByCode($value)->getValue()
                : '';
        }, $codes);

        return array_filter($output);
    }
}
