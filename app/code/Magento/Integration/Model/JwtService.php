<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Integration\Model;


use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Easy\Build;
use Jose\Easy\Load;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class JwtService implements TokenServiceInterface
{
    /**
     * @var DirectoryList
     */
    private $dirList;

    /**
     * @var JWK|null
     */
    private $jwk;

    /**
     * @var JWK|null
     */
    private $pubJwk;

    /**
     * JwtService constructor.
     * @param DirectoryList $dirList
     */
    public function __construct(DirectoryList $dirList)
    {
        $this->dirList = $dirList;
    }

    public function generate(CustomerInterface $customer): string
    {
        $jwt = Build::jws()
            ->alg(new RS256())
            ->iss('magento.com', true)
            ->exp(time() + 3600, true)
            ->claim('customer_id', (string)$customer->getId());

        return $jwt->sign($this->loadPrivateJwk());
    }

    public function load(string $token): string
    {
        try {
            $jwt = Load::jws($token)
                ->alg(new RS256())
                ->iss('magento.com')
                ->exp()
                ->claim('customer_id', function (string $id) { return (bool)$id; })
                ->key($this->loadPublicJwk());

            $tokenData = $jwt->run();

            return $tokenData->claims->get('customer_id');
        } catch (\Throwable $ex) {
            throw new LocalizedException(__('Failed to decrypt, [error: %1]', $ex->getMessage()));
        }
    }

    private function loadPrivateJwk(): JWK
    {
        if (!$this->jwk) {
            $this->jwk = JWKFactory::createFromKeyFile(
                $this->dirList->getPath(DirectoryList::VAR_DIR) .'/privatersa.pem',
                'secret',
                ['use' => 'sig']
            );
        }

        return $this->jwk;
    }

    private function loadPublicJwk(): JWK
    {
        if (!$this->pubJwk) {
            $this->pubJwk = JWKFactory::createFromKeyFile(
                $this->dirList->getPath(DirectoryList::VAR_DIR) .'/publicrsa.pem',
                'secret',
                ['use' => 'sig']
            );
        }

        return $this->pubJwk;
    }
}
