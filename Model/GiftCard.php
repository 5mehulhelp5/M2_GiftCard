<?php

namespace Market\GiftCard\Model;

use Magento\Framework\Model\AbstractModel;
use Market\GiftCard\Api\Data\GiftCardInterface;

class GiftCard extends AbstractModel implements GiftCardInterface
{

    public const  STATUS_ACTIVE = 1;
    public const STATUS_USED = 2;
    public const  STATUS_EXPIRED = 3;

    public function _construct()
    {
        $this->_init(ResourceModel\GiftCard::class);
    }

    public function getCustomerId(): int
    {
        return (int)$this->getData(self::ASSIGNED_CUSTOMER_ID);
    }
    public function setCustomerId(int $value): void
    {
        $this->setData(self::ASSIGNED_CUSTOMER_ID, $value);
    }

    public function getCode(): string
    {
        return (string)$this->getData(self::CODE);
    }
    public function setCode(string $value): void
    {
        $this->setData(self::CODE, $value);
    }

    public function getStatus(): int
    {
        return (int)$this->getData(self::STATUS);
    }

    public function setStatus(int $value): void
    {
        $this->setData(self::STATUS, $value);
    }

    public function getInitialValue(): float
    {
        return (float)$this->getData(self::INITIAL_VALUE);
    }

    public function setInitialValue(float $value): void
    {
        $this->setData(self::INITIAL_VALUE, $value);
    }

    public function getCurrentValue(): float
    {
        return (float)$this->getData(self::CURRENT_VALUE);
    }

    public function setCurrentValue(float $value): void
    {
        $this->setData(self::CURRENT_VALUE, $value);
    }

    public function getCreatedAt(): \DateTime
    {
        return new \DateTime($this->getData(self::CREATED_AT));
    }

    public function setCreatedAt(\DateTime $value): void
    {
        $this->setData(self::CREATED_AT, $value->format('Y-m-d H:i:s'));
    }

    public function getUpdatedAt(): \DateTime
    {
        return new \DateTime($this->getData(self::UPDATED_AT));
    }

    public function setUpdatedAt(\DateTime $value): void
    {
        $this->setData(self::UPDATED_AT, $value->format('Y-m-d H:i:s'));
    }

    public function setRecipientName(string $value): void
    {
        $this->setData(self::RECIPIENT_NAME, $value);
    }

    public function getRecipientName(): string
    {
        return (string)$this->getData(self::RECIPIENT_NAME);
    }

    public function setRecipientEmail(string $value): void
    {
        $this->setData(self::RECIPIENT_EMAIL, $value);
    }

    public function getRecipientEmail():string
    {
        return (string)$this->getData(self::RECIPIENT_EMAIL);
    }
}
