<?php
/**
 * Copyright © Market. All rights reserved.
 */
declare(strict_types=1);

namespace Market\GiftCard\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Gift card email configuration accessor.
 */
class Email
{
    /**#@+
     * Configuration paths for gift card email settings.
     */
    public const XML_PATH_ENABLED = 'catalog/giftcard/enabled';
    public const XML_PATH_EMAIL_IDENTITY = 'catalog/giftcard/email_identity';
    public const XML_PATH_EMAIL_TEMPLATE = 'catalog/giftcard/email_template';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Whether gift card emails are enabled for the given store.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Sender email identity (e.g. general, sales, support).
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailIdentity($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_IDENTITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Email template identifier.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailTemplate($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
