<?php

namespace Market\GiftCard\Model;

class Attributes
{
    public const IS_CUSTOM_Allowed = 'is_custom_allowed';

    public const OPTION_RECIPIENT_NAME = 'recipient_name';
    public const OPTION_RECIPIENT_EMAIL = 'recipient_email';
    public const OPTION_AMOUNT = 'amount';

    public const OPTION_LIST = [
        self::OPTION_RECIPIENT_NAME,
        self::OPTION_RECIPIENT_EMAIL,
        self::OPTION_AMOUNT
    ];
}
