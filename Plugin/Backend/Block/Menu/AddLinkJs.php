<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Plugin\Backend\Block\Menu;

use Magento\Backend\Block\Menu;

class AddLinkJs
{
    /**
     * @param Menu $subject
     * @param string $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(Menu $subject, $html)
    {
        $js = $subject->getLayout()->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Jajuma_HyvaFaq::backend/menu/link_blank.phtml')
            ->toHtml();

        return $html . $js;
    }
}
