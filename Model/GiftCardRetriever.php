<?php

namespace Market\GiftCard\Model;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Model\PaymentDetailsFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Market\GiftCard\Api\GiftCardRetrieverInterface;

class GiftCardRetriever implements GiftCardRetrieverInterface
{
    private QuoteIdMaskFactory $quoteIdMaskFactory;
    private GiftCardRepository $giftCardRepository;
    private CartRepositoryInterface $cartRepository;
    /**
     * @var \Magento\Checkout\Model\PaymentDetailsFactory
     */
    protected PaymentDetailsFactory $paymentDetailsFactory;
    private PaymentMethodManagementInterface $paymentMethodManagement;
    private CartTotalRepositoryInterface $cartTotalsRepository;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        GiftCardRepository $giftCardRepository,
        CartRepositoryInterface $cartRepository,
        PaymentDetailsFactory $paymentDetailsFactory,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalsRepository,
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->giftCardRepository = $giftCardRepository;
        $this->cartRepository = $cartRepository;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalsRepository = $cartTotalsRepository;
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function applyGuest(string $cartId, string $giftCardCode): PaymentDetailsInterface
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->applyCustomer((int)$quoteIdMask->getQuoteId(), $giftCardCode);
    }

    /**
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function applyCustomer(int $cartId, string $giftCardCode): PaymentDetailsInterface
    {
        $giftCard = $this->giftCardRepository->getByCode($giftCardCode);

        if ($giftCard->getStatus() === GiftCard::STATUS_USED
        || $giftCard->getCurrentValue() <= 0) {
            throw new StateException(__('This Gift Card has already been used.'));
        }

        $cart = $this->cartRepository->get($cartId);
        $cart->getExtensionAttributes()->setGiftCardId($giftCard->getId());
        $this->cartRepository->save($cart);


        /** @var PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cart->getId()));
        return $paymentDetails;
    }
}
