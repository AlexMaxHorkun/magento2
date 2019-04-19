<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

$request = [
    'order_shipment' => new \Magento\Framework\DataObject(
        [
            'order' => new \Magento\Framework\DataObject(['increment_id' => '1']),
            'order_id' => '2',
            'store_id' => '1',
            'customer_id' => '1',
            'billing_address_id' => '4',
            'shipping_address_id' => '3',
            'global_currency_code' => 'USD',
            'base_currency_code' => 'USD',
            'store_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'store_to_base_rate' => '0.0000',
            'store_to_order_rate' => '0.0000',
            'base_to_global_rate' => '1.0000',
            'base_to_order_rate' => '1.0000',
            'discount_description' => NULL,
            'items' =>
                [
                    0 => [
                        'order_item_id' => '2',
                        'product_id' => '2',
                        'sku' => 'testR',
                        'name' => 'testR',
                        'description' => NULL,
                        'weight' => '2.0000',
                        'price' => '11.0000',
                        'base_price' => '11.0000',
                        'price_incl_tax' => '11.0000',
                        'base_price_incl_tax' => '11.0000',
                        'qty' => 3,
                        'parent_id' => NULL,
                        'store_id' => '1',
                    ],
                ],
            'total_qty' => 3,
            'packages' =>
                [1 => [
                    'params' =>
                        [
                            'container' => '00',
                            'weight' => '2',
                            'customs_value' => '11',
                            'length' => '22',
                            'width' => '22',
                            'height' => '22',
                            'weight_units' => 'POUND',
                            'dimension_units' => 'INCH',
                            'content_type' => '',
                            'content_type_other' => '',
                            'delivery_confirmation' => '0',
                        ],
                    'items' =>
                        [2 => [
                            'qty' => '1',
                            'customs_value' => '11',
                            'price' => '11.0000',
                            'name' => 'testR',
                            'weight' => '2.0000',
                            'product_id' => '2',
                            'order_item_id' => '2',
                        ],
                        ],
                ],
                    2 => [
                        'params' =>
                            [
                                'container' => '00',
                                'weight' => '2',
                                'customs_value' => '11',
                                'length' => '22',
                                'width' => '22',
                                'height' => '22',
                                'weight_units' => 'POUND',
                                'dimension_units' => 'INCH',
                                'content_type' => '',
                                'content_type_other' => '',
                                'delivery_confirmation' => '0',
                            ],
                        'items' =>
                            [2 =>
                                [
                                    'qty' => '1',
                                    'customs_value' => '11',
                                    'price' => '11.0000',
                                    'name' => 'testR',
                                    'weight' => '2.0000',
                                    'product_id' => '2',
                                    'order_item_id' => '2',
                                ],
                            ],
                    ],
                    3 => [
                        'params' =>
                            [
                                'container' => '00',
                                'weight' => '2',
                                'customs_value' => '11',
                                'length' => '22',
                                'width' => '22',
                                'height' => '22',
                                'weight_units' => 'POUND',
                                'dimension_units' => 'INCH',
                                'content_type' => '',
                                'content_type_other' => '',
                                'delivery_confirmation' => '0',
                            ],
                        'items' =>
                            [
                                2 =>
                                    [
                                        'qty' => '1',
                                        'customs_value' => '11',
                                        'price' => '11.0000',
                                        'name' => 'testR',
                                        'weight' => '2.0000',
                                        'product_id' => '2',
                                        'order_item_id' => '2',
                                    ],
                            ],
                    ],
                ],
        ]),
    'shipper_contact_person_name' => 'admin admin',
    'shipper_contact_person_first_name' => 'admin',
    'shipper_contact_person_last_name' => 'admin',
    'shipper_contact_company_name' => 'TestStore',
    'shipper_contact_phone_number' => '5123334422',
    'shipper_email' => 'admin@test.com',
    'shipper_address_street' => '11410 Century Oaks Terrace',
    'shipper_address_street_1' => '11410 Century Oaks Terrace',
    'shipper_address_street_2' => NULL,
    'shipper_address_city' => 'Austin',
    'shipper_address_state_or_province_code' => 'TX',
    'shipper_address_postal_code' => '78758',
    'shipper_address_country_code' => 'US',
    'recipient_contact_person_name' => 'test1 test1',
    'recipient_contact_person_first_name' => 'test1',
    'recipient_contact_person_last_name' => 'test1',
    'recipient_contact_company_name' => NULL,
    'recipient_contact_phone_number' => '5124443322',
    'recipient_email' => 'test1@test.com',
    'recipient_address_street' => '10515 N Mopac Expy',
    'recipient_address_street_1' => '10515 N Mopac Expy',
    'recipient_address_street_2' => '',
    'recipient_address_city' => 'Austin',
    'recipient_address_state_or_province_code' => 'TX',
    'recipient_address_region_code' => 'TX',
    'recipient_address_postal_code' => '78759',
    'recipient_address_country_code' => 'US',
    'shipping_method' => '03',
    'package_weight' => '2',
    'packages' =>
        [1 => [
            'params' =>
                [
                    'container' => '00',
                    'weight' => '2',
                    'customs_value' => '11',
                    'length' => '22',
                    'width' => '22',
                    'height' => '22',
                    'weight_units' => 'POUND',
                    'dimension_units' => 'INCH',
                    'content_type' => '',
                    'content_type_other' => '',
                    'delivery_confirmation' => '0',
                ],
            'items' =>
                [2 => [
                    'qty' => '1',
                    'customs_value' => '11',
                    'price' => '11.0000',
                    'name' => 'testR',
                    'weight' => '2.0000',
                    'product_id' => '2',
                    'order_item_id' => '2',
                ],
                ],
        ],
            2 => [
                'params' =>
                    [
                        'container' => '00',
                        'weight' => '2',
                        'customs_value' => '11',
                        'length' => '22',
                        'width' => '22',
                        'height' => '22',
                        'weight_units' => 'POUND',
                        'dimension_units' => 'INCH',
                        'content_type' => '',
                        'content_type_other' => '',
                        'delivery_confirmation' => '0',
                    ],
                'items' =>
                    [
                        2 =>
                            [
                                'qty' => '1',
                                'customs_value' => '11',
                                'price' => '11.0000',
                                'name' => 'testR',
                                'weight' => '2.0000',
                                'product_id' => '2',
                                'order_item_id' => '2',
                            ],
                    ],
            ],
            3 => [
                'params' =>
                    [
                        'container' => '00',
                        'weight' => '2',
                        'customs_value' => '11',
                        'length' => '22',
                        'width' => '22',
                        'height' => '22',
                        'weight_units' => 'POUND',
                        'dimension_units' => 'INCH',
                        'content_type' => '',
                        'content_type_other' => '',
                        'delivery_confirmation' => '0',
                    ],
                'items' =>
                    [
                        2 =>
                            [
                                'qty' => '1',
                                'customs_value' => '11',
                                'price' => '11.0000',
                                'name' => 'testR',
                                'weight' => '2.0000',
                                'product_id' => '2',
                                'order_item_id' => '2',
                            ],
                    ],
            ],
        ],
    'base_currency_code' => 'USD',
    'store_id' => '1',
    'package_id' => 1,
    'packaging_type' => '00',
    'package_params' => new \Magento\Framework\DataObject(
        [
            'container' => '00',
            'weight' => '2',
            'customs_value' => '11',
            'length' => '22',
            'width' => '22',
            'height' => '22',
            'weight_units' => 'POUND',
            'dimension_units' => 'INCH',
            'content_type' => '',
            'content_type_other' => '',
            'delivery_confirmation' => '0',
        ]
    ),
];

$requests3 = new \Magento\Shipping\Model\Shipment\Request($request);
$request['packages'] = array_fill(0, 5, $request['packages'][1]);
$requests5 = new \Magento\Shipping\Model\Shipment\Request($request);
$request['packages'] = array_fill(0, 10, $request['packages'][1]);
$requests10 = new \Magento\Shipping\Model\Shipment\Request($request);

return [
    '3 packages' => [$requests3],
    '5 packages' => [$requests5],
    '10 packages' => [$requests10],
];
