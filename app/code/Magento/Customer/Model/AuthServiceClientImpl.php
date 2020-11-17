<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Model;

use Grpc\ChannelCredentials;
use Magento\Customer\Api\AuthServiceClientInterface;
use Magento\Customer\Api\Data\TokenCheckedInterface;
use Magento\Framework\Exception\LocalizedException;

class AuthServiceClientImpl implements AuthServiceClientInterface
{
    public function checkToken(string $token): TokenCheckedInterface
    {
        try {
            $client = new AuthServiceClient('127.0.0.1:9001', ['credentials' => ChannelCredentials::createInsecure()]);
            $started = microtime(true);
            $arg = new TokenArg();
            $arg->setToken($token);
            /** @var TokenResponse $resp */
            $response = $client->introspect($arg)->wait();

            return new TokenChecked(
                $response[0]->getTokenFound(),
                (microtime(true) - $started) * 1000,
                $response[0]->getFound()->getCustomerId(),
                $response[0]->getFound()->getFirstName(),
                $response[0]->getFound()->getLastName()
            );
        } catch (\Throwable $exception) {
            throw new LocalizedException(__('Failed to call grpc, [error: %1]', $exception->getMessage()));
        }
    }
}
