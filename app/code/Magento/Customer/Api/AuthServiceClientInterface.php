<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Api;

use Magento\Customer\Api\Data\TokenCheckedInterface;

interface AuthServiceClientInterface
{
    /**
     * @param string $token
     * @return TokenCheckedInterface
     */
    public function checkToken(string $token): TokenCheckedInterface;
}
