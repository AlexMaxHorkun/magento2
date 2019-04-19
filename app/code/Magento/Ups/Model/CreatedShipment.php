<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

/**
 * Result of creating  shipment.
 */
class CreatedShipment
{
    /**
     * @var string
     */
    private $trackingNumber;

    /**
     * @var string
     */
    private $labelContent;

    /**
     * CreatedShipment constructor.
     * @param string $trackingNumber
     * @param string $labelContent
     */
    public function __construct(string $trackingNumber, string $labelContent)
    {
        $this->trackingNumber = $trackingNumber;
        $this->labelContent = $labelContent;
    }

    /**
     * Tracking ID.
     *
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * Label to print.
     *
     * @return string
     */
    public function getLabelContent(): string
    {
        return $this->labelContent;
    }
}
