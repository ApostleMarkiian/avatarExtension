<?php

namespace Recruitment\CustomerAvatar\Api;

interface AvatarInterface
{
    /**
     * Get customer avatar by customer_id
     *
     * @return string
     */
    public function getCustomerAvatarById($customer_id = false);
}
