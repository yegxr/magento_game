<?php

namespace Perspective\Game\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ChatGptApiHelper extends AbstractHelper
{
    const XML_PATH_CHATGPT = 'sales/chatgpt_game/';

    /**
     * @param mixed $field
     * @param null|string|int $storeId
     * @return mixed
     */
    protected function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getGptApi($storeId = null): mixed
    {
        return $this->getConfigValue(self::XML_PATH_CHATGPT . 'chatgpt_api_key', $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getGptUrl($storeId = null): mixed
    {
        return $this->getConfigValue(self::XML_PATH_CHATGPT . 'chatgpt_url_key', $storeId);
    }
}
