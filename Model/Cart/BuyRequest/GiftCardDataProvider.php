<?php
declare(strict_types=1);

namespace Market\GiftCard\Model\Cart\BuyRequest;

use Magento\Quote\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;
use Magento\Quote\Model\Cart\Data\CartItem;
use Market\GiftCard\Model\Attributes;

class GiftCardDataProvider implements BuyRequestDataProviderInterface
{
    private const OPTION_TYPE = 'giftcard';

    public function execute(CartItem $cartItem): array
    {
        $result = [];

        foreach ($cartItem->getEnteredOptions() ?? [] as $option) {
            $optionData = \explode('/', base64_decode($option->getUid()));

            if ($optionData[0] !== self::OPTION_TYPE) {
                continue;
            }

            $key = $optionData[1] ?? null;

            if ($key && in_array($key, Attributes::OPTION_LIST, true)) {
                $result[$key] = $option->getValue();
            }
        }

        return $result;
    }
}
