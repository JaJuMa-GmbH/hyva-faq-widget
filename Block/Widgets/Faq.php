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
     * @param Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Filter\Template $templateProcessor,
        FilterProvider $filterProvider,
        Data $advancedWidgetHelper,
        Conditions $conditionsHelper,
        array $data = []
    ) {
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
     * @param string $answer
     * @return string
     * @throws Exception
     */
    public function processorAnswerContent($answer)
    {
        return $this->filterProvider->getPageFilter()->filter(
            $answer
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

    public function isEnabledStructuredData()
    {
        return ($this->advancedWidgetHelper->getConfig('hyvafaq/general/is_enabled_structured_data') == '1' ? true : false);
    }

    public function getHeroicons($openCloseIcons, $isShowQuestionOnDesktop = 0)
    {
        $funcs = [];
        $icons = [];
        switch ($openCloseIcons) {
            case 1:
                $funcs['type'] = 'chevron';
                if ($isShowQuestionOnDesktop) {
                    $icons[] = ['name' => 'chevronDownHtml', 'class' => ''];
                } else {
                    $icons[] = ['name' => 'chevronUpHtml', 'class' => ''];
                }
                $funcs['icons'] = $icons;
                break;
            case 2:
                $funcs['type'] = 'plus-minus';
                $icons[] = ['name' => 'plusHtml', 'class' => ':not(.active)'];
                $icons[] = ['name' => 'minusHtml', 'class' => '.active'];
                $funcs['icons'] = $icons;
                break;
            case 3:
                $funcs['type'] = 'plus-x';
                $icons[] = ['name' => 'plusHtml', 'class' => ':not(.active)'];
                $icons[] = ['name' => 'xHtml', 'class' => '.active'];
                $funcs['icons'] = $icons;
                break;
            case 4:
                $funcs['type'] = 'plusCircle-minusCircle';
                $icons[] = ['name' => 'plusCircleHtml', 'class' => ':not(.active)'];
                $icons[] = ['name' => 'minusCircleHtml', 'class' => '.active'];
                $funcs['icons'] = $icons;
                break;
            case 5:
                $funcs['type'] = 'plusCircle-xCircle';
                $icons[] = ['name' => 'plusCircleHtml', 'class' => ':not(.active)'];
                $icons[] = ['name' => 'xCircleHtml', 'class' => '.active'];
                $funcs['icons'] = $icons;
                break;
        }

        return $funcs;
    }
}
