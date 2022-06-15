<?php

namespace Recruitment\CustomerAvatar\Model\Attribute\Backend;

use Recruitment\CustomerAvatar\Model\Source\Validation\Image;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class Avatar extends AbstractBackend
{
    /**
     * @param $object
     * @return Avatar
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave($object)
    {
        $validation = new Image();
        $attrCode = $this->getAttribute()->getAttributeCode();

        if ($attrCode == 'profile_picture') {
            if ($validation->isImageValid('tmpp_name', $attrCode) === false) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The profile picture is not a valid image.')
                );
            }
        }

        return parent::beforeSave($object);
    }
}
