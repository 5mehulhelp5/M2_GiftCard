<?php

namespace Market\GiftCard\Controller\Adminhtml\View;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Market\GiftCard\Controller\Adminhtml\AbstractGiftCard;

/**
 * Index action.
 */
class Index extends AbstractGiftCard implements HttpGetActionInterface
{
    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Market_GiftCard::giftcards');
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Cards'));

        return $resultPage;
    }
}
