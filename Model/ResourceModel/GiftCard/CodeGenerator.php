<?php

namespace Market\GiftCard\Model\ResourceModel\GiftCard;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Market\GiftCard\Api\Data\GiftCardInterface;

class CodeGenerator extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(GiftCardInterface::TABLE_NAME, GiftCardInterface::ID);
    }

    public function getNewCode(): string
    {
        do {
            $code  = $this->generateCode();
            $select = $this->getConnection()->select()
                ->from($this->getMainTable(), 'id')
                ->where('code = ?', $code);

        } while ((bool)$this->getConnection()->fetchOne($select));

        return $code;
    }

    private function generateCode(): string
    {
        return  bin2hex(random_bytes(10));
    }
}
