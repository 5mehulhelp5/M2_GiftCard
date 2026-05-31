<?php
/**
 * Copyright © Market. All rights reserved.
 */
declare(strict_types=1);

namespace Market\GiftCard\Model\Email;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Market\GiftCard\Api\Data\GiftCardInterface;
use Market\GiftCard\Model\Config\Email as EmailConfig;
use Psr\Log\LoggerInterface;

class EmailSender
{
    /**
     * @var EmailConfig
     */
    private EmailConfig $emailConfig;

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var SenderResolverInterface
     */
    private SenderResolverInterface $senderResolver;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var Emulation
     */
    private Emulation $emulation;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @param EmailConfig $emailConfig
     * @param TransportBuilder $transportBuilder
     * @param SenderResolverInterface $senderResolver
     * @param StateInterface $inlineTranslation
     * @param Emulation $emulation
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        EmailConfig $emailConfig,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver,
        StateInterface $inlineTranslation,
        Emulation $emulation,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        UrlInterface $urlBuilder,
    ) {
        $this->emailConfig = $emailConfig;
        $this->transportBuilder = $transportBuilder;
        $this->senderResolver = $senderResolver;
        $this->inlineTranslation = $inlineTranslation;
        $this->emulation = $emulation;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param GiftCardInterface $giftCard
     * @param int|null $storeId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function send(
        GiftCardInterface $giftCard,
        $storeId = null
    ): bool {
        if ($storeId === null) {
            $storeId = (int)$this->storeManager->getStore()->getId();
        }

        if (!$this->emailConfig->isEnabled($storeId)) {
            return false;
        }

        $this->inlineTranslation->suspend();

        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        try {
            $from = $this->senderResolver->resolve(
                $this->emailConfig->getEmailIdentity($storeId),
                $storeId
            );

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->emailConfig->getEmailTemplate($storeId))
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars(
                    [
                        'url' => $this->urlBuilder->getUrl('giftcard/' . $giftCard->getCode()),
                        'giftCard' => $giftCard,
                        'recipientName' => $giftCard->getRecipientName(),
                        'recipientEmail' => $giftCard->getRecipientEmail(),
                    ]
                )
                ->setFromByScope($from, $storeId)
                ->addTo($giftCard->getRecipientEmail(), $giftCard->getRecipientName())
                ->getTransport();

            $transport->sendMessage();
        } catch (LocalizedException $e) {
            $recipientEmail = $giftCard->getRecipientEmail();
            $this->logger->error(
                'Gift card email could not be sent: ' . $e->getMessage(),
                ['recipient' => $recipientEmail, 'store_id' => $storeId]
            );
            return false;
        } finally {
            $this->emulation->stopEnvironmentEmulation();
            $this->inlineTranslation->resume();
        }

        return true;
    }
}
