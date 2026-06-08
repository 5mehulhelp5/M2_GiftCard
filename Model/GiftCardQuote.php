<?php

namespace Market\GiftCard\Model;

use Magento\Catalog\Model\AbstractModel;
use Market\GiftCard\Api\Data\GiftCardQuoteInterface;

class GiftCardQuote extends AbstractModel implements GiftCardQuoteInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\GiftCardQuote::class);
    }

    public function getGiftCardId(): int
    {
        return (int)$this->getData(self::GIFT_CARD_ID);
    }

    public function setGiftCardId(int $value): void
    {
        $this->setData(self::GIFT_CARD_ID, $value);
    }

    public function getQuoteId(): int
    {
        return (int)$this->getData(self::QUOTE_ID);
    }

    public function setQuoteId(int $value): void
    {
        $this->setData(self::QUOTE_ID, $value);
    }

}
