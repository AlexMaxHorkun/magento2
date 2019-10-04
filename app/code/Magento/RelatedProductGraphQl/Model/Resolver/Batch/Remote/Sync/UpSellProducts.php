<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\Sync;

use Magento\Catalog\Model\Product\Link;

class UpSellProducts extends AbstractLinkedProducts
{
    /**
     * @inheritDoc
     */
    protected function getLinkType(): int
    {
        return Link::LINK_TYPE_UPSELL;
    }
}
