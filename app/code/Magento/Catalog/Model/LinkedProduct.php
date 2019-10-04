<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Catalog\Model;

use Magento\Catalog\Api\Data\LinkedProductInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class LinkedProduct implements LinkedProductInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductInterface
     */
    private $linked;

    /**
     * LinkedProduct constructor.
     * @param int $id
     * @param ProductInterface $linked
     */
    public function __construct(int $id, ProductInterface $linked)
    {
        $this->id = $id;
        $this->linked = $linked;
    }

    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     * @return ProductInterface|Product
     */
    public function getLinked(): \Magento\Catalog\Api\Data\ProductInterface
    {
        return $this->linked;
    }

}
