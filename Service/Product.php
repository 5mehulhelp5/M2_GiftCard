<?php

namespace Market\GiftCard\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Product
{
    /** @var ProductRepositoryInterface  */
    private ProductRepositoryInterface $productRepository;

    /** @var RequestInterface  */
    private RequestInterface $request;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        RequestInterface $request
    ) {
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getProduct(): ProductInterface
    {
        if (!$this->request->getParam('id')) {
            throw new NoSuchEntityException(__('Product ID is required.'));
        }

        $productId = $this->request->getParam('id');
        return $this->productRepository->getById($productId);
    }
}
