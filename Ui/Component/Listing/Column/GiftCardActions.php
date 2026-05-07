<?php
declare(strict_types=1);

namespace Market\GiftCard\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class GiftCardActions extends Column
{
    /** Url path */
    public const  GIFTCARD_URL_PATH_EDIT = 'giftcard/edit';
    public const  GIFTCARD_URL_PATH_DELETE = 'giftcard/delete';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        private readonly Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $name = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['id'])) {
                continue;
            }

            $title = $this->escaper->escapeHtml($item['code'] ?? (string) $item['id']);

            $item[$name]['edit'] = [
                'href' => $this->urlBuilder->getUrl(self::GIFTCARD_URL_PATH_EDIT, ['id' => $item['id']]),
                'label' => __('Edit'),
            ];

            $item[$name]['delete'] = [
                'href' => $this->urlBuilder->getUrl(self::GIFTCARD_URL_PATH_DELETE, ['id' => $item['id']]),
                'label' => __('Delete'),
                'confirm' => [
                    'title' => __('Delete %1', $title),
                    'message' => __('Are you sure you want to delete a %1 record?', $title),
                ],
                'post' => true,
            ];
        }

        return $dataSource;
    }
}
