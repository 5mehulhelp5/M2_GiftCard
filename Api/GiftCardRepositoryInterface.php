<?php

namespace Market\GiftCard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Market\GiftCard\Api\Data\GiftCardInterface;
use Market\GiftCard\Api\Data\GiftCardSearchResultsInterface;

interface GiftCardRepositoryInterface
{
    /**
     * @param int $id
     * @return \Market\GiftCard\Api\Data\GiftCardInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): GiftCardInterface;

    /**
     * @param string $code
     * @return \Market\GiftCard\Api\Data\GiftCardInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): GiftCardInterface;

    /**
     * @param GiftCardInterface $giftCard
     * @param int|null $storeId
     * @return GiftCardInterface
     * @throws CouldNotSaveException
     */
    public function save(GiftCardInterface $giftCard, int $storeId = null): GiftCardInterface;

    /**
     * @param \Market\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(GiftCardInterface $giftCard): bool;

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): bool;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Market\GiftCard\Api\Data\GiftCardSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): GiftCardSearchResultsInterface;
}
