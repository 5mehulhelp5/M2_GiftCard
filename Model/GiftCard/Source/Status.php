<?php

namespace Market\GiftCard\Model\GiftCard\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Market\GiftCard\Model\GiftCard;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => GiftCard::STATUS_ACTIVE,  'label' => __('Active')],
            ['value' => GiftCard::STATUS_USED,    'label' => __('Used')],
            ['value' => GiftCard::STATUS_EXPIRED, 'label' => __('Expired')],
        ];
    }
}
