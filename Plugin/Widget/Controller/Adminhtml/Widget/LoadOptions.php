<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Plugin\Widget\Controller\Adminhtml\Widget;

use Closure;
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

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ViewInterface $view
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ViewInterface $view
    )
    {
        $this->view = $view;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions $subject
     * @param Closure $proceed
     */
    public function aroundExecute(
        \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions $subject,
        Closure $proceed
    )
    {
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
                        $request['values'] = array_map('htmlspecialchars_decode', $request['values']);
                        if (isset($request['values']['conditions_encoded'])) {
                            $request['values']['conditions'] =
                                $this->getConditionsHelper()->decode($request['values']['conditions_encoded']);
                        }
                        if ($optionsBlock->getWidgetType() == 'Jajuma\HyvaFaq\Block\Widgets\Faq') {
                            $helper = $this->objectManager->create(\Jajuma\HyvaFaq\Helper\Data::class);
                            foreach ($request['values'] as $key => $value) {
                                if ($key == 'questions') {
                                    $request['values'][$key] = $helper->decodeWidgetValues($value);
                                }
                            }
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
