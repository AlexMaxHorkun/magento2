<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Integration\Model\JwtService;
use Magento\Integration\Model\TokenServiceInterface;
use Spiral\GRPC;

class AuthService implements AuthServiceInterface
{
    /**
     * @var TokenServiceInterface
     */
    private $jwtService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $repo;

    /**
     * AuthService constructor.
     * @param TokenServiceInterface $jwtService
     * @param CustomerRepositoryInterface $repo
     */
    public function __construct(TokenServiceInterface $jwtService, CustomerRepositoryInterface $repo)
    {
        $this->jwtService = $jwtService;
        $this->repo = $repo;
    }

    public function introspect(GRPC\ContextInterface $ctx, TokenArg $in): TokenResponse
    {
        $tokenFound = false;
        $data = null;
        $response = new TokenResponse();

        try {
            $customer = $this->repo->getById((int) $this->jwtService->load($in->getToken()));
            $tokenFound = true;
            $data = new CustomerData();
            $data->setCustomerId((string) $customer->getId());
            $data->setFirstName($customer->getFirstname());
            $data->setLastName($customer->getLastname());
        } catch (\Throwable $ex) {
            //Not found
        }
        $response->setTokenFound($tokenFound);
        $response->setFound($data);

        return $response;
    }
}
