<?php

namespace Market\GiftCard\Api\Data;

interface GiftCardQuoteInterface
{

    public const TABLE_NAME = 'market_gift_card_quote';
    public const ID = 'id';
    public const GIFT_CARD_ID = 'gift_card_id';
    public const QUOTE_ID = 'quote_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return void
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getGiftCardId(): int;

    /**
     * @param int $value
     * @return void
     */
    public function setGiftCardId(int $value): void;

    /**
     * @return int
     */
    public function getQuoteId(): int;

    /**
     * @param int $value
     * @return void
     */
    public function setQuoteId(int $value): void;

}
