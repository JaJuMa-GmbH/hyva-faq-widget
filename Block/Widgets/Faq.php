<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Block\Widgets;

use Exception;
use Jajuma\HyvaFaq\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;

class Faq extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Jajuma_HyvaFaq::faq.phtml';

    /**
     * @var \Magento\Framework\Filter\Template
     */
    protected $templateProcessor;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var Data
     */
    protected $advancedWidgetHelper;

    /**
     * @var Conditions
     */
    private $conditionsHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Filter\Template $templateProcessor
     * @param FilterProvider $filterProvider
     * @param Data $advancedWidgetHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Filter\Template $templateProcessor,
        FilterProvider $filterProvider,
        Data $advancedWidgetHelper,
        Conditions $conditionsHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->templateProcessor = $templateProcessor;
        $this->filterProvider = $filterProvider;
        $this->advancedWidgetHelper = $advancedWidgetHelper;
        $this->conditionsHelper = $conditionsHelper;
    }

    /**
     * @param string $html
     * @return string
     * @throws Exception
     */
    public function processorHtml($html)
    {
        return $this->filterProvider->getPageFilter()->filter(
            $this->advancedWidgetHelper->decodeWidgetValues($html)
        );
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');
        return $conditions ? $this->conditionsHelper->decode($conditions) : [];
    }

    /**
     * @return Data
     */
    public function getAdvancedWidgetHelper()
    {
        return $this->advancedWidgetHelper;
    }

    public function isEnabled()
    {
        return ($this->advancedWidgetHelper->getConfig('hyvafaq/general/is_enabled') == '1' ? true : false);
    }
}