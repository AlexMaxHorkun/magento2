<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

use Magento\Shipping\Model\Shipment\Request;

/**
 * Creator of shipments, uses UPS gateway.
 */
interface ShipmentCreatorInterface
{
    /**
     * Ship packages.
     *
     * @param Request[] $requests
     * @throws ShipmentCreationException
     * @return CreatedShipment[]
     */
    public function create(array $requests): array;
}
