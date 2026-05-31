<?php

namespace Market\GiftCard\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Market\GiftCard\Api\GiftCardRepositoryInterface;
use Market\GiftCard\Model\Attributes;
use Market\GiftCard\Model\GiftCard as GiftCardModel;
use Market\GiftCard\Model\GiftCardFactory as GiftCardFactoryModel;
use Market\GiftCard\Model\ResourceModel\GiftCard\CodeGenerator;
use Market\GiftCard\Model\Type\GiftCard;

class RegisterGiftCard implements ObserverInterface
{

    /** @var CollectionFactory  */
    private CollectionFactory $productCollectionFactory;

    /** @var ProductInterface[]  */
    private array $productCache = [];

    /** @var OrderItemRepositoryInterface  */
    private OrderItemRepositoryInterface $orderItemRepository;

    /** @var OrderItemInterface[]  */
    private array $orderItemCache = [];

    /** @var GiftCardFactoryModel  */
    private GiftCardFactoryModel $giftCardFactory;

    /** @var GiftCardRepositoryInterface  */
    private GiftCardRepositoryInterface $giftCardRepository;

    /** @var CodeGenerator  */
    private CodeGenerator $codeGenerator;

    public function __construct(
        CollectionFactory            $productCollectionFactory,
        OrderItemRepositoryInterface $orderItemRepository,
        GiftCardFactoryModel         $giftCardFactory,
        GiftCardRepositoryInterface $giftCardRepository,
        CodeGenerator $codeGenerator
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardRepository = $giftCardRepository;
        $this->codeGenerator = $codeGenerator;
    }

    public function execute(Observer $observer): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $observer->getData('invoice');

        $giftCardItems = array_filter(iterator_to_array($invoice->getItems()), function (InvoiceItemInterface $item) {
            if (!$item->getProductId()) {
                return false;
            }
            $product =  $this->getProduct((int)$item->getProductId());
            return $product->getTypeId() === GiftCard::TYPE_CODE;
        });

        if (!count($giftCardItems)) {
            return;
        }

        foreach ($giftCardItems as $giftCardItem) {
            $this->createGiftCard($giftCardItem);
        }

    }

    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function createGiftCard(InvoiceItemInterface $item): void
    {
        $giftCard = $this->giftCardFactory->create();
        $orderItem = $this->getOrderItem($item);

        $recipientName = (string) $orderItem->getProductOptionByCode(Attributes::OPTION_RECIPIENT_NAME);
        $recipientEmail = (string) $orderItem->getProductOptionByCode(Attributes::OPTION_RECIPIENT_EMAIL);
        $amount = $orderItem->getProductOptionByCode(Attributes::OPTION_AMOUNT);

        if (!$recipientEmail) {
            throw new NoSuchEntityException(__('Recipient Email is not found.'));
        }

        $value = $item->getQty() * ($orderItem->getRowTotal() / $orderItem->getQtyOrdered());
        $giftCard->setCurrentValue($value);
        $giftCard->setInitialValue($value);
        $giftCard->setRecipientEmail($recipientEmail);
        $giftCard->setRecipientName($recipientName);
        $giftCard->setCode($this->codeGenerator->getNewCode());
        $giftCard->setStatus(GiftCardModel::STATUS_ACTIVE);

        $this->giftCardRepository->save($giftCard, (int)$orderItem->getStoreId());
    }

    private function getOrderItem(InvoiceItemInterface $item): OrderItemInterface
    {
        if (method_exists($item, 'getOrderItemId') && $item->getOrderItem()) {
            return $item->getOrderItem();
        }

        $orderItemId = $item->getOrderItemId();

        if (isset($this->orderItemCache[$orderItemId])) {
            return $this->orderItemCache[$orderItemId];
        }

        $this->orderItemCache[$orderItemId] =  $this->orderItemRepository->get($orderItemId);

        return $this->orderItemCache[$orderItemId];
    }

    private function getProduct(int $id): DataObject|ProductInterface
    {

        if (isset($this->productCache[$id])) {
            return $this->productCache[$id];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', $id);
        $collection->setPageSize(1);
        $this->productCache[$id] = $collection->getFirstItem();

        return $this->productCache[$id];
    }
}
