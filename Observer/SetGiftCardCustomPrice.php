<?php
declare(strict_types=1);

namespace Market\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Market\GiftCard\Model\Attributes;
use Market\GiftCard\Model\Type\GiftCard;

class SetGiftCardCustomPrice implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        foreach ($quote->getAllAddresses() as $address) {
            foreach ($address->getAllItems() as $item) {
                if (
                    $item->getProductType() !== GiftCard::TYPE_CODE
                    || !$item->getOptionByCode(Attributes::OPTION_AMOUNT)
                    || !$item->getOptionByCode(Attributes::OPTION_AMOUNT)->getValue()
                ) {
                    continue;
                }

                $amount = (float) $item->getOptionByCode(Attributes::OPTION_AMOUNT)->getValue();
                $item->setCustomPrice($amount);
                $item->setOriginalCustomPrice($amount);
                $item->getProduct()->setIsSuperMode(true);
            }
        }
    }
}
