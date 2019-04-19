<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

/**
 * When failed to create shipments.
 */
class ShipmentCreationException extends \RuntimeException
{
    /**
     * @var string[]
     */
    private $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Failed to create shipments');
    }

    /**
     * Errors that occurred, aggregated.
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
