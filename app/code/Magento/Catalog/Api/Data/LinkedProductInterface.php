<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Catalog\Api\Data;

interface LinkedProductInterface
{
    /**
     * @return int
     */
    public function getProductId(): int;

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getLinked(): \Magento\Catalog\Api\Data\ProductInterface;
}
