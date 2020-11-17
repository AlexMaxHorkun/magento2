<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Console\Command;

use Magento\Customer\Model\AuthService;
use Magento\Customer\Model\AuthServiceInterface;
use Spiral\Goridge\StreamRelay;
use Spiral\GRPC\Server;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunAuthService extends Command
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        parent::__construct('customer:grpc:auth-service');
        $this->authService = $authService;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('display_errors', 'stderr');
        $server = new Server();
        $server->registerService(AuthServiceInterface::class, $this->authService);

        $worker = new Worker(new StreamRelay(STDIN, STDOUT));
        $server->serve($worker);
    }
}
