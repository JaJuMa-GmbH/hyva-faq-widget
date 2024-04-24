<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Plugin\Widget\Controller\Adminhtml\Widget;

use Closure;
use Jajuma\HyvaFaq\Helper\Data;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Widget\Helper\Conditions;

class LoadOptions
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var Conditions
     */
    private $conditionsHelper;

    protected $advancedWidgetHelper;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ViewInterface $view
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ViewInterface $view,
        Data $advancedWidgetHelper
    ) {
        $this->view = $view;
        $this->objectManager = $objectManager;
        $this->advancedWidgetHelper = $advancedWidgetHelper;
    }

    /**
     * @param \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions $subject
     * @param Closure $proceed
     */
    public function aroundExecute(
        \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions $subject,
        Closure $proceed
    ) {
        try {
            $this->view->loadLayout();
            if ($paramsJson = $subject->getRequest()->getParam('widget')) {
                $request = $this->objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->view->getLayout()->getBlock('wysiwyg_widget.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
                        if ($optionsBlock->getWidgetType() != 'Jajuma\HyvaFaq\Block\Widgets\Faq') {
                            $request['values'] = array_map('htmlspecialchars_decode', $request['values']);
                        }
                        if (isset($request['values']['conditions_encoded'])) {
                            $conditions = $this->getConditionsHelper()->decode($request['values']['conditions_encoded']);
                            $conditions = array_map(function ($condition) {
                                $questionLists = $condition['question_lists'];
                                $newQuestionLists = array_map(function($item) {
                                    if ($this->advancedWidgetHelper->isBase64($item['question_answer'])){
                                        $item['question_answer'] = base64_decode($item['question_answer']);
                                    }
                                    return $item;
                                }, $questionLists);
                                $condition['question_lists'] = $newQuestionLists;
                                return $condition;
                            }, $conditions);

                            $request['values']['conditions'] = $conditions;
                        }
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                }
                $this->view->renderLayout();
            }
        } catch (LocalizedException $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $subject->getResponse()->representJson(
                $this->objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
            );
        }
    }

    /**
     * @return \Magento\Widget\Helper\Conditions
     */
    private function getConditionsHelper()
    {
        if (!$this->conditionsHelper) {
            $this->conditionsHelper = ObjectManager::getInstance()->get(\Magento\Widget\Helper\Conditions::class);
        }

        return $this->conditionsHelper;
    }
}
