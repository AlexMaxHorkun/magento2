<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Model;


use Magento\Customer\Api\Data\TokenCheckedInterface;

class TokenChecked implements TokenCheckedInterface
{
    /**
     * @var bool
     */
    private $found;

    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $firstname;

    /**
     * @var string|null
     */
    private $lastname;

    /**
     * @var float
     */
    private $elapsed;

    /**
     * TokenChecked constructor.
     * @param bool $found
     * @param string|null $id
     * @param string|null $firstname
     * @param string|null $lastname
     * @param float $elapsed
     */
    public function __construct(bool $found, float $elapsed, ?string $id, ?string $firstname, ?string $lastname)
    {
        $this->found = $found;
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->elapsed = $elapsed;
    }

    public function getIsFound(): bool
    {
        return $this->found;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function getElapsed(): float
    {
        return $this->elapsed;
    }

}
