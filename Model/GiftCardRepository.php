<?php

namespace Market\GiftCard\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Market\GiftCard\Api\Data\GiftCardInterface;
use Market\GiftCard\Api\Data\GiftCardSearchResultsInterface;
use Market\GiftCard\Api\Data\GiftCardSearchResultsInterfaceFactory;
use Market\GiftCard\Api\GiftCardRepositoryInterface;
use Market\GiftCard\Model\Email\EmailSender;
use Market\GiftCard\Model\ResourceModel\GiftCard as GiftCardResource;
use Market\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;

class GiftCardRepository implements GiftCardRepositoryInterface
{
    /**
     * @var GiftCardResource
     */
    private GiftCardResource $resource;

    /**
     * @var GiftCardFactory
     */
    private GiftCardFactory $giftCardFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var GiftCardSearchResultsInterfaceFactory
     */
    private GiftCardSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;
    private EmailSender $emailSender;

    public function __construct(
        GiftCardResource $resource,
        GiftCardFactory $giftCardFactory,
        CollectionFactory $collectionFactory,
        GiftCardSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        EmailSender $emailSender
    ) {
        $this->resource = $resource;
        $this->giftCardFactory = $giftCardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->emailSender = $emailSender;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): GiftCardInterface
    {
        $giftCard = $this->giftCardFactory->create();
        $this->resource->load($giftCard, $id);

        if (!$giftCard->getId()) {
            throw new NoSuchEntityException(__('Gift card with ID "%1" does not exist.', $id));
        }

        return $giftCard;
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $code): GiftCardInterface
    {
        $giftCard = $this->giftCardFactory->create();
        $this->resource->load($giftCard, $code, GiftCardInterface::CODE);

        if (!$giftCard->getId()) {
            throw new NoSuchEntityException(__('Gift card with code "%1" does not exist.', $code));
        }

        return $giftCard;
    }

    /**
     * @inheritDoc
     */
    public function save(GiftCardInterface $giftCard, int $storeId = null): GiftCardInterface
    {
        try {
            $canNotify = !(bool)$giftCard->getId();

            $this->resource->save($giftCard);

            if ($canNotify) {
                $this->emailSender->send($giftCard, $storeId);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save gift card: %1', $e->getMessage()), $e);
        }

        return $giftCard;
    }

    /**
     * @inheritDoc
     */
    public function delete(GiftCardInterface $giftCard): bool
    {
        try {
            $this->resource->delete($giftCard);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete gift card: %1', $e->getMessage()), $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): GiftCardSearchResultsInterface
    {
        /** @var \Market\GiftCard\Model\ResourceModel\GiftCard\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Market\GiftCard\Api\Data\GiftCardSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
