<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Integration\Model;

use Magento\Customer\Api\Data\CustomerInterface;

interface TokenServiceInterface
{
    public function generate(CustomerInterface $customer): string;

    public function load(string $token): string;
}
