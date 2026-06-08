<?php

namespace Market\GiftCard\Model\ResourceModel\GiftCardQuote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Market\GiftCard\Model\GiftCardQuote as GiftCardQuoteModel;
use Market\GiftCard\Model\ResourceModel\GiftCardQuote as GiftCardQuoteResourceModel;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            GiftCardQuoteModel::class,
            GiftCardQuoteResourceModel::class
        );
    }
}
