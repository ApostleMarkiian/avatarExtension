<?php

namespace Recruitment\CustomerAvatar\Block\Attributes;

use Magento\Customer\Model\Customer;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\MediaStorage\Helper\File\Storage;
use Recruitment\CustomerAvatar\Helper\Config;

class Avatar extends Template
{
    /**
     * Default avatar api url
     */
    const CUSTOMER_DEFAULT_AVATAR_URL = 'https://avatars.abstractapi.com/v1/';

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Core file storage
     *
     * @var Storage
     */
    protected $coreFileStorage;

    /**
     * @var AbstractBlock
     */
    protected $viewFileUrl;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Storage $coreFileStorage
     * @param Repository $viewFileUrl
     * @param Config $configHelper
     * @param SessionFactory $sessionFactory
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Storage $coreFileStorage,
        Repository $viewFileUrl,
        Config $configHelper,
        SessionFactory $sessionFactory,
        Customer $customer
    ) {
        $this->_filesystem = $filesystem;
        $this->coreFileStorage = $coreFileStorage;
        $this->viewFileUrl = $viewFileUrl;
        $this->configHelper = $configHelper;
        $this->sessionFactory = $sessionFactory->create();
        $this->customer = $customer;
        parent::__construct($context);
    }

    /**
     * @return boolean
     */
    public function checkImageFile($file)
    {
        $file = base64_decode($file);
        $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath($fileName);

        if (!$directory->isFile($fileName)
            && !$this->coreFileStorage->processStorageFile($path)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getAvatarCurrentCustomer($file)
    {
        if ($this->checkImageFile(base64_encode($file)) === true) {
            return $this->getUrl('viewfile/avatar/view/', ['image' => base64_encode($file)]);
        }

        return $this->getDefaultCustomerAvatarImageUrl();
    }

    /**
     * Get customer avatar by customer_id
     *
     * @return string
     */
    public function getCustomerAvatarById($customer_id = false)
    {
        if ($customer_id) {
            $customerDetail = $this->customer->load($customer_id);

            if ($customerDetail && !empty($customerDetail->getProfilePicture())) {
                if ($this->checkImageFile(base64_encode($customerDetail->getProfilePicture())) === true) {

                    return $this->getUrl(
                        'viewfile/avatar/view/',
                        ['image' => base64_encode($customerDetail->getProfilePicture())]
                    );
                }
            }
        }

        return $this->getDefaultCustomerAvatarImageUrl();
    }

    /**
     * Returning customer avatar default image url
     *
     * @return string
     */
    public function getDefaultCustomerAvatarImageUrl($customerData = null): string
    {
        $customerName = $customerData ? $customerData->getName() : '';

        if (!$customerData){
            $customerData = $this->customer->load($this->sessionFactory->getId());

            $customerName = $customerData->getData('firstname') . ' ' . $customerData->getData('lastname');
        }

        return self::CUSTOMER_DEFAULT_AVATAR_URL .
            '?api_key=' . $this->configHelper->getCustomerAvatarImageApiKey() .
            '&name=' . $customerName;
    }
}
