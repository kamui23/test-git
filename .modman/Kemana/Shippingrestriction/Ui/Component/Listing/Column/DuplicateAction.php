<?php

namespace Kemana\Shippingrestriction\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 */
class DuplicateAction extends Column
{
    const DUPLICATE_LABEL = 'Duplicate';
    const DUPLICATE_URL   = 'kemana_shippingrestriction/rule/duplicate';
    const EDIT_LABEL      = 'Edit';
    const EDIT_URL        = 'kemana_shippingrestriction/rule/edit';
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['rule_id'])) {
                    $item[$this->getData('name')] = [
                        'duplicate' => [
                            'href'   => $this->_urlBuilder->getUrl(
                                self::DUPLICATE_URL,
                                ['id' => $item['rule_id']]
                            ),
                            'label'  => __(self::DUPLICATE_LABEL),
                            'hidden' => false,
                        ],
                        'edit'      => [
                            'href'   => $this->_urlBuilder->getUrl(
                                self::EDIT_URL,
                                ['id' => $item['rule_id']]
                            ),
                            'label'  => __(self::EDIT_LABEL),
                            'hidden' => true,
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
