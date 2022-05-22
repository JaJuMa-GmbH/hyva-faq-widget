<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Plugin\Widget\Block\Adminhtml\Widget;

class Options
{
    public function beforeSetWidgetValues(\Magento\Widget\Block\Adminhtml\Widget\Options $subject, $value)
    {
        return [$value];
    }
}
