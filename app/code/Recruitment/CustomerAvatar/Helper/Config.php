<?php
declare(strict_types=1);

namespace Recruitment\CustomerAvatar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Returning API key for default customer avatar images
     *
     * @return string|false
     */
    public function getCustomerAvatarImageApiKey()
    {
        return $this->scopeConfig->getValue(
            'customer/customer_avatar_image/customer_avatar_image_api_key',
            ScopeInterface::SCOPE_STORE
        ) ?: false;
    }
}
