<?php

namespace Market\GiftCard\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Market\GiftCard\Api\Data\GiftCardQuoteInterface;

class GiftCardQuote extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            GiftCardQuoteInterface::TABLE_NAME,
            GiftCardQuoteInterface::ID
        );
    }

    /**
     * @throws LocalizedException
     */
    public function add(int $quoteId, ?int $giftCardId): void
    {
        $isSet = (bool)$this->get($quoteId);

        if ($isSet && $giftCardId) {
            //update existing record
            $connection = $this->getConnection();
            $connection->update(
                $this->getMainTable(),
                [GiftCardQuoteInterface::GIFT_CARD_ID => $giftCardId],
                $this->getConnection()->quoteInto(
                    GiftCardQuoteInterface::QUOTE_ID . ' = ?',
                    $quoteId
                )
            );
        } elseif (!$isSet && !$giftCardId) {
            //delete existing record
            $connection = $this->getConnection();
            $connection->delete(
                $this->getMainTable(),
                $this->getConnection()->quoteInto(
                    GiftCardQuoteInterface::QUOTE_ID . ' = ?',
                    $quoteId
                )
            );
        } elseif ($giftCardId) {
            //insert new record
            $connection = $this->getConnection();
            $connection->insert(
                $this->getMainTable(),
                [
                    GiftCardQuoteInterface::QUOTE_ID => $quoteId,
                    GiftCardQuoteInterface::GIFT_CARD_ID => $giftCardId
                ]
            );
        }

    }

    /**
     * @throws LocalizedException
     */
    public function get(int $quoteId): ?int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), GiftCardQuoteInterface::GIFT_CARD_ID)
            ->where(GiftCardQuoteInterface::QUOTE_ID . ' = ?', $quoteId);

        $value = $connection->fetchOne($select);
        return $value ? (int)$value : null;
    }
}
