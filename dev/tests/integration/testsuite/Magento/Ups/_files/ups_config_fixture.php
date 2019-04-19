<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var Writer $configWriter */
$configWriter = $objectManager->get(WriterInterface::class);
/** @var EncryptorInterface $encryptor */
$encryptor = $objectManager->get(EncryptorInterface::class);

$configWriter->save('carriers/ups/active', 1);
$configWriter->save('carriers/ups/active','1');
$configWriter->save('carriers/ups/active_rma','0');
$configWriter->save('carriers/ups/type','UPS_XML');
$configWriter->save('carriers/ups/mode_xml','0');
$configWriter->save('carriers/ups/username',$encryptor->encrypt(getenv('MAGE_UPS_USER')));
$configWriter->save('carriers/ups/access_license_number',$encryptor->encrypt(getenv('MAGE_UPS_LICENSE')));
$configWriter->save('carriers/ups/password',$encryptor->encrypt(getenv('MAGE_UPS_PASS')));
$configWriter->save('carriers/ups/shipper_number',getenv('MAGE_UPS_NUMBER'));
$configWriter->save('carriers/ups/handling_fee',null);
$configWriter->save('carriers/ups/free_shipping_enable','0');
$configWriter->save('carriers/ups/specificcountry',null);
$configWriter->save('carriers/ups/showmethod','0');
$configWriter->save('carriers/ups/debug','0');
$configWriter->save('carriers/ups/sort_order',null);

$scopeConfig = $objectManager->get(ScopeConfigInterface::class);
$scopeConfig->clean();
