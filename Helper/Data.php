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

    /**
     * Check if a string is valid Base64 encoded
     *
     * @param string $string
     * @return bool
     */
    public function isBase64($string): bool
    {
        if ($string === '' || !is_string($string)) {
            return false;
        }

        // Optimization: only handle whitespace if present
        if (strpbrk($string, " \t\r\n") !== false) {
            $string = preg_replace('/\s/', '', $string);
            if ($string === '') {
                return false;
            }
        }

        // Length must be a multiple of 4
        if (strlen($string) % 4 !== 0) {
            return false;
        }

        // Accurate regex for standard Base64 format
        if (!preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $string)) {
            return false;
        }

        // Strict decode
        $decoded = base64_decode($string, true);
        if ($decoded === false) {
            return false;
        }

        // Compare with rtrim - safe and accurate
        return rtrim(base64_encode($decoded), '=') === rtrim($string, '=');
    }
}
