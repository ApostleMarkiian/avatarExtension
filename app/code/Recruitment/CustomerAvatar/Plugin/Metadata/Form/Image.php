<?php

namespace Recruitment\CustomerAvatar\Plugin\Metadata\Form;

class Image
{
    /**
     * @param \Magento\Customer\Model\Metadata\Form\Image $subject
     * @param $value
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeExtractValue(\Magento\Customer\Model\Metadata\Form\Image $subject, $value)
    {
        $attrCode = $subject->getAttribute()->getAttributeCode();

        if ($this->isImageValid('tmp_name', $attrCode) === false) {
            $_FILES[$attrCode]['tmpp_name'] = $_FILES[$attrCode]['tmp_name'];

            unset($_FILES[$attrCode]['tmp_name']);
        }

        return [$value];
    }

    /**
     * @param $tmp_name, $attrCode
     * @return bool
     */
    public function isImageValid($tmp_name, $attrCode)
    {
        if ($attrCode == 'profile_picture') {
            if (!empty($_FILES[$attrCode][$tmp_name])) {
                $imageFile = @getimagesize($_FILES[$attrCode][$tmp_name]);

                if ($imageFile === false) {
                    return false;
                } else {
                    $valid_types = ['image/gif', 'image/jpeg', 'image/png'];

                    if (!in_array($imageFile['mime'], $valid_types)) {
                        return false;
                    }
                }

                return true;
            }
        }

        return true;
    }
}
