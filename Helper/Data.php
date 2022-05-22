<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2022-present JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\HyvaFaq\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $_reservedData = [
        'type',
        'name_in_layout',
        'area',
        'module_name',
        'name',
        '_is_changed',
        '_renderer_name'
    ];

    /**
     * Get store configuration
     *
     * @param string $configPath
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param array|string $values
     * @return array|bool|string
     */
    public function decodeWidgetValues($values)
    {
        if (!is_array($values)) {
            return base64_decode(strtr($values, ':_-', '+/='));
        }

        foreach ($values as $key => $value) {
            if (is_scalar($value) && !in_array($key, $this->_reservedData)) {
                $values[$key] = base64_decode(strtr($values, ':_-', '+/='));
            }
        }

        return $values;
    }

    /**
     * @param array|string $values
     * @return array|string
     */
    public function encodeWidgetValues($values)
    {
        if (!is_array($values) && !in_array($values, $this->_reservedData)) {
            return strtr(base64_encode($values), '+/=', ':_-');
        }

        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                $values[$key] = strtr(base64_encode($value), '+/=', ':_-');
            }
        }

        return $values;
    }
}
