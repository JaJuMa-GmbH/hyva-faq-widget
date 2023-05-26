<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Plugin\Widget\Controller\Adminhtml\Widget;

use Closure;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Widget\Model\Widget;
use Jajuma\HyvaFaq\Helper\Data;

class BuildWidget
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * BuildWidget constructor.
     * @param Widget $widget
     * @param Data $helper
     * @param File $file
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Widget $widget,
        Data $helper,
        File $file,
        DirectoryList $directoryList
    ) {
        $this->widget = $widget;
        $this->helper = $helper;
        $this->file = $file;
        $this->directoryList = $directoryList;
    }

    /**
     * @param \Magento\Widget\Controller\Adminhtml\Widget\BuildWidget $subject
     * @param Closure $proceed
     */
    public function aroundExecute(
        \Magento\Widget\Controller\Adminhtml\Widget\BuildWidget $subject,
        Closure $proceed
    ) {
        $type = $subject->getRequest()->getPost('widget_type');
        $params = $subject->getRequest()->getPost('parameters', []);
        $asIs = $subject->getRequest()->getPost('as_is');

        if ($type == 'Jajuma\HyvaFaq\Block\Widgets\Faq') {
            foreach ($params as $key => $value) {
                if ($key == 'questions') {
                    $this->_processQuestion($value);
                    $params[$key] = $this->helper->encodeWidgetValues($value);
                }
            }
        }

        $html = $this->widget->getWidgetDeclaration($type, $params, $asIs);
        $subject->getResponse()->setBody($html);
    }

    protected function _processQuestion(&$value)
    {
        if (count($value)) {
            foreach ($value as $groupId => $questionGroups) {
                foreach ($questionGroups['question_lists'] as $questionId => $question) {
                    $value[$groupId]['question_lists'][$questionId]['question_answer'] = base64_encode($question['question_answer']);
                }
            }
            $value = json_encode($value);
        }
    }
}
