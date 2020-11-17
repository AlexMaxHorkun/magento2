<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Integration\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\Oauth\Token;

class OpaqueTokenService implements TokenServiceInterface
{
    /**
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param TokenModelFactory $tokenModelFactory
     * @param DateTime $dateTime
     */
    public function __construct(TokenModelFactory $tokenModelFactory, DateTime $dateTime)
    {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->dateTime = $dateTime;
    }

    public function generate(CustomerInterface $customer): string
    {
        /** @var Token $token */
        $token = $this->tokenModelFactory->create();

        return $token->createCustomerToken($customer->getId())->getToken();
    }

    public function load(string $token): string
    {
        /** @var Token $tokenModel */
        $tokenModel = $this->tokenModelFactory->create();
        $tokenModel = $tokenModel->loadByToken($token);

        if (!$tokenModel->getId()
            || $tokenModel->getRevoked()
        ) {
            throw new LocalizedException(__('Invalid token'));
        }

        return (string) $tokenModel->getCustomerId();
    }

}
