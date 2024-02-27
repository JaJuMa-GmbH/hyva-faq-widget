<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Block\Widgets\Faq;

use Jajuma\HyvaFaq\Helper\Data;
use Magento\Backend\Block\Widget\Button;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form\Element\AbstractElement as Element;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Framework\Data\Form\Element\Editor;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Helper\Conditions;

class Questions extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Jajuma_HyvaFaq::widget/faq/questions.phtml';

    /**
     * @var FormElementFactory
     */
    protected $elementFactory;

    /**
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * @var Data
     */
    protected $advancedWidgetHelper;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Conditions
     */
    private $conditionsHelper;

    /**
     * Items constructor.
     * @param TemplateContext $context
     * @param FormElementFactory $elementFactory
     * @param Config $wysiwygConfig
     * @param Data $advancedWidgetHelper
     * @param Registry $coreRegistry
     * @param Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        FormElementFactory $elementFactory,
        Config $wysiwygConfig,
        Data $advancedWidgetHelper,
        Registry $coreRegistry,
        Conditions $conditionsHelper,
        $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->advancedWidgetHelper = $advancedWidgetHelper;
        $this->coreRegistry = $coreRegistry;
        $this->conditionsHelper = $conditionsHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        //add wysiwyg to get MediabrowserUtility
        /** @var Editor $wysiwyg */
        $element->setData('after_element_html', $this->toHtml());
        return $element;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getAddItemButtonHtml()
    {
        /** @var Button $button * */
        $button = $this->getLayout()->createBlock(Button::class);
        $button->setData(
            [
                'label' => __('Add New Question Group'),
                'onclick' => 'JajumaWidgetFAQInstance.addItemGroup({})',
                'class' => 'action-add',
            ]
        );
        return $button->toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getAddQuestionButtonHtml()
    {
        /** @var Button $button * */
        $button = $this->getLayout()->createBlock(Button::class);
        $button->setData(
            [
                'label' => __('Add New Question'),
                'onclick' => 'JajumaWidgetFAQInstance.addQuestion({}, this)',
                'class' => 'action-add',
            ]
        );
        return $button->toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getRemoveQuestionButtonHtml()
    {
        /** @var Button $button * */
        $button = $this->getLayout()->createBlock(Button::class);
        $button->setData(
            [
                'label' => $this->escapeHtmlAttr(__('Remove Question')),
                'onclick' => 'JajumaWidgetFAQInstance.removeQuestionItem(this)',
                'class' => 'action-delete',
            ]
        );
        return $button->toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getRemoveLayoutButtonHtml()
    {
        /** @var Button $button * */
        $button = $this->getLayout()->createBlock(Button::class);
        $button->setData(
            [
                'label' => $this->escapeHtmlAttr(__('Remove')),
                'onclick' => 'JajumaWidgetFAQInstance.removeItem(this)',
                'class' => 'action-delete',
            ]
        );
        return $button->toHtml();
    }

    /**
     * @return array
     */
    public function getWidgetContentValues()
    {
        if ($this->getRequest()->getActionName() != 'edit') {
            $paramsJson = $this->getRequest()->getParam('widget');
            $params = json_decode($paramsJson, true);
            if (!empty($params['values'])) {
                return $params['values'];
            }
        } else {
            /** @var Instance $widget * */
            $widget = $this->coreRegistry->registry('current_widget_instance');
            $params = $widget->getWidgetParameters();
            return $params;
        }
        return [];
    }

    /**
     * @return Data
     */
    public function getAdvancedWidgetHelper()
    {
        return $this->advancedWidgetHelper;
    }

    /**
     * @return Data
     */
    public function getConditionsHelper()
    {
        return $this->conditionsHelper;
    }

    public function decode($content) {
        $widgetDecodedArr = $this->conditionsHelper->decode($content);
        $widgetDecodedArr = array_map(function ($condition) {
            $questionLists = $condition['question_lists'];
            $newQuestionLists = array_map(function($item) {
                if ($this->advancedWidgetHelper->isBase64($item['question_answer'])){
                    $item['question_answer'] = base64_decode($item['question_answer']);
                }
                return $item;
            }, $questionLists);
            $condition['question_lists'] = $newQuestionLists;
            return $condition;
        }, $widgetDecodedArr);
        return $widgetDecodedArr;
    }
}
