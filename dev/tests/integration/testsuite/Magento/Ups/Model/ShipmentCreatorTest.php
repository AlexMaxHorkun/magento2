<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

use Magento\Shipping\Model\Shipment\Request;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test shipment creation.
 */
class ShipmentCreatorTest extends TestCase
{
    /**
     * @var Carrier
     */
    private $asyncCreatorCarrier;

    /**
     * @var Carrier
     */
    private $syncCreatorCarrier;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();

        $asyncShipmentFactory = new class($objectManager) extends ShipmentCreatorInterfaceFactory {
            private $objectManager;

            public function __construct($objectManager)
            {
                $this->objectManager = $objectManager;
            }

            public function create(array $data = [])
            {
                return call_user_func_array([$this->objectManager, 'create'], [AsyncShipmentCreator::class, $data]);
            }
        };
        $syncShipmentFactory = new class($objectManager) extends ShipmentCreatorInterfaceFactory {
            private $objectManager;

            public function __construct($objectManager)
            {
                $this->objectManager = $objectManager;
            }

            public function create(array $data = [])
            {
                return call_user_func_array([$this->objectManager, 'create'], [SyncShipmentCreator::class, $data]);
            }
        };

        $this->asyncCreatorCarrier = $objectManager->create(
            Carrier::class,
            ['shipmentCreatorFactory' => $asyncShipmentFactory]
        );
        $this->syncCreatorCarrier = $objectManager->create(
            Carrier::class,
            ['shipmentCreatorFactory' => $syncShipmentFactory]
        );
    }

    /**
     * Test sending multiple requests with same address.
     *
     * @param Request $request
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Ups/_files/ups_config_fixture.php
     * @dataProvider getRequests()
     */
    public function testSameAddress(Request $request)
    {
        $numberOfPackages = count($request->getPackages());
        $started = microtime(true);
        $created = $this->asyncCreatorCarrier->requestToShipment(clone $request);
        $finished = microtime(true);
        $asyncTime = round(($finished - $started) * 1000);
        $this->assertNotEmpty($created->getInfo());
        $this->assertCount($numberOfPackages, $created->getInfo());
        echo PHP_EOL .$numberOfPackages .' async requests with the same address took ' . $asyncTime . ' ms' . PHP_EOL;
        //Now with sync requests
        $started = microtime(true);
        $created = $this->syncCreatorCarrier->requestToShipment(clone $request);
        $finished = microtime(true);
        $this->assertNotEmpty($created->getInfo());
        $this->assertCount($numberOfPackages, $created->getInfo());
        $syncTime = round(($finished - $started) * 1000);
        echo PHP_EOL .$numberOfPackages .' sync requests with the same address took ' . $syncTime . ' ms' . PHP_EOL;

        if ($asyncTime < $syncTime) {
            $winner = 'NEW Asynchronous';
        } else {
            $winner = 'OLD Synchronous';
        }
        echo PHP_EOL . $winner .' shipment creator is faster by ' .(abs($asyncTime - $syncTime)) .' ms' . PHP_EOL;
    }

    /**
     * Variations
     *
     * @return array
     */
    public function getRequests(): array
    {
        return include __DIR__ .'/../_files/ups_requests.php';
    }
}
