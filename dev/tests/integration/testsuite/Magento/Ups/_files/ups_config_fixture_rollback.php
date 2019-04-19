<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Writer  $configWriter */
$configWriter = $objectManager->create(WriterInterface::class);

$configWriter->delete('carriers/ups/access_license_number');
$configWriter->delete('carriers/ups/active');
$configWriter->delete('carriers/ups/active_rma');
$configWriter->delete('carriers/ups/debug');
$configWriter->delete('carriers/ups/free_shipping_enable');
$configWriter->delete('carriers/ups/handling_fee');
$configWriter->delete('carriers/ups/mode_xml');
$configWriter->delete('carriers/ups/password');
$configWriter->delete('carriers/ups/shipper_number');
$configWriter->delete('carriers/ups/showmethod');
$configWriter->delete('carriers/ups/sort_order');
$configWriter->delete('carriers/ups/specificcountry');
$configWriter->delete('carriers/ups/type');
$configWriter->delete('carriers/ups/username');
