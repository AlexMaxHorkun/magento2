<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Simplexml\Element as Xml;
use Magento\Shipping\Model\Simplexml\ElementFactory as XmlFactory;
use Magento\Ups\Helper\Config as ConfigHelper;

/**
 * @inheritdoc
 */
abstract class AbstractShipmentCreator implements ShipmentCreatorInterface
{
    protected const DELIVERY_CONFIRMATION_SHIPMENT = 1;

    protected const DELIVERY_CONFIRMATION_PACKAGE = 2;

    protected const USA_COUNTRY_ID = 'US';

    /**
     * @var XmlFactory
     */
    private $xmlFactory;

    /**
     * @var array
     */
    private $configData;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param XmlFactory $xmlFactory
     * @param array $configData
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        XmlFactory $xmlFactory,
        array $configData,
        ConfigHelper $configHelper
    ) {
        $this->xmlFactory = $xmlFactory;
        $this->configData = $configData;
        $this->configHelper = $configHelper;
    }

    /**
     * Get delivery confirmation level based on origin/destination
     *
     * Return null if delivery confirmation is not acceptable
     *
     * @param string|null $countyDestination
     * @return int|null
     */
    private function getDeliveryConfirmationLevel(?string $countyDestination = null)
    {
        if ($countyDestination === null) {
            return null;
        }

        if ($countyDestination == self::USA_COUNTRY_ID) {
            return self::DELIVERY_CONFIRMATION_PACKAGE;
        }

        return self::DELIVERY_CONFIRMATION_SHIPMENT;
    }

    /**
     * Check is Country U.S. Possessions and Trust Territories
     *
     * @param string $countyId
     * @return boolean
     */
    private function isUSCountry(string $countyId): bool
    {
        switch ($countyId) {
            case 'AS':
                // Samoa American
            case 'GU':
                // Guam
            case 'MP':
                // Northern Mariana Islands
            case 'PW':
                // Palau
            case 'PR':
                // Puerto Rico
            case 'VI':
                // Virgin Islands US
            case 'US':
                // United States
                return true;
        }

        return false;
    }


    /**
     * Prepare quotes request.
     *
     * @param Request $request
     * @return string
     */
    protected final function prepareQuoteXmlRequest(Request $request): string
    {
        $phonePattern = '/[\s\_\-\(\)]+/';
        $phoneNumber = $request->getShipperContactPhoneNumber();
        /** @var int $phoneNumber */
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setShipperContactPhoneNumber($phoneNumber);
        $phoneNumber = $request->getRecipientContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setRecipientContactPhoneNumber($phoneNumber);

        $packageParams = $request->getPackageParams();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == \Zend_Measure_Weight::POUND ? 'LBS' : 'KGS';
        $dimensionsUnits = $packageParams->getDimensionUnits() == \Zend_Measure_Length::INCH ? 'IN' : 'CM';

        $itemsDesc = [];
        $itemsShipment = $request->getPackageItems();
        foreach ($itemsShipment as $itemShipment) {
            $item = new \Magento\Framework\DataObject();
            $item->setData($itemShipment);
            $itemsDesc[] = $item->getName();
        }

        /** @var Xml $xmlRequest */
        $xmlRequest = $this->xmlFactory->create(
            ['data' => '<?xml version = "1.0" ?><ShipmentConfirmRequest xml:lang="en-US"/>']
        );
        $requestPart = $xmlRequest->addChild('Request');
        $requestPart->addChild('RequestAction', 'ShipConfirm');
        $requestPart->addChild('RequestOption', 'nonvalidate');

        $shipmentPart = $xmlRequest->addChild('Shipment');
        if ($request->getIsReturn()) {
            $returnPart = $shipmentPart->addChild('ReturnService');
            // UPS Print Return Label
            $returnPart->addChild('Code', '9');
        }
        $shipmentPart->addChild('Description', substr(implode(' ', $itemsDesc), 0, 35));
        //empirical

        $shipperPart = $shipmentPart->addChild('Shipper');
        if ($request->getIsReturn()) {
            $shipperPart->addChild('Name', $request->getRecipientContactCompanyName());
            $shipperPart->addChild('AttentionName', $request->getRecipientContactPersonName());
            $shipperPart->addChild('ShipperNumber', $this->configData['shipper_number']);
            $shipperPart->addChild('PhoneNumber', $request->getRecipientContactPhoneNumber());

            $addressPart = $shipperPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getRecipientAddressStreet());
            $addressPart->addChild('AddressLine2', $request->getRecipientAddressStreet2());
            $addressPart->addChild('City', $request->getRecipientAddressCity());
            $addressPart->addChild('CountryCode', $request->getRecipientAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getRecipientAddressPostalCode());
            if ($request->getRecipientAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getRecipientAddressStateOrProvinceCode());
            }
        } else {
            $shipperPart->addChild('Name', $request->getShipperContactCompanyName());
            $shipperPart->addChild('AttentionName', $request->getShipperContactPersonName());
            $shipperPart->addChild('ShipperNumber', $this->configData['shipper_number']);
            $shipperPart->addChild('PhoneNumber', $request->getShipperContactPhoneNumber());

            $addressPart = $shipperPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getShipperAddressStreet());
            $addressPart->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $addressPart->addChild('City', $request->getShipperAddressCity());
            $addressPart->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }
        }

        $shipToPart = $shipmentPart->addChild('ShipTo');
        $shipToPart->addChild('AttentionName', $request->getRecipientContactPersonName());
        $shipToPart->addChild(
            'CompanyName',
            $request->getRecipientContactCompanyName() ? $request->getRecipientContactCompanyName() : 'N/A'
        );
        $shipToPart->addChild('PhoneNumber', $request->getRecipientContactPhoneNumber());

        $addressPart = $shipToPart->addChild('Address');
        $addressPart->addChild('AddressLine1', $request->getRecipientAddressStreet1());
        $addressPart->addChild('AddressLine2', $request->getRecipientAddressStreet2());
        $addressPart->addChild('City', $request->getRecipientAddressCity());
        $addressPart->addChild('CountryCode', $request->getRecipientAddressCountryCode());
        $addressPart->addChild('PostalCode', $request->getRecipientAddressPostalCode());
        if ($request->getRecipientAddressStateOrProvinceCode()) {
            $addressPart->addChild('StateProvinceCode', $request->getRecipientAddressRegionCode());
        }
        if ($this->configData['dest_type'] == 'RES') {
            $addressPart->addChild('ResidentialAddress');
        }

        if ($request->getIsReturn()) {
            $shipFromPart = $shipmentPart->addChild('ShipFrom');
            $shipFromPart->addChild('AttentionName', $request->getShipperContactPersonName());
            $shipFromPart->addChild(
                'CompanyName',
                $request->getShipperContactCompanyName() ? $request
                    ->getShipperContactCompanyName() : $request
                    ->getShipperContactPersonName()
            );
            $shipFromAddress = $shipFromPart->addChild('Address');
            $shipFromAddress->addChild('AddressLine1', $request->getShipperAddressStreet1());
            $shipFromAddress->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $shipFromAddress->addChild('City', $request->getShipperAddressCity());
            $shipFromAddress->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $shipFromAddress->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $shipFromAddress->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }

            $addressPart = $shipToPart->addChild('Address');
            $addressPart->addChild('AddressLine1', $request->getShipperAddressStreet1());
            $addressPart->addChild('AddressLine2', $request->getShipperAddressStreet2());
            $addressPart->addChild('City', $request->getShipperAddressCity());
            $addressPart->addChild('CountryCode', $request->getShipperAddressCountryCode());
            $addressPart->addChild('PostalCode', $request->getShipperAddressPostalCode());
            if ($request->getShipperAddressStateOrProvinceCode()) {
                $addressPart->addChild('StateProvinceCode', $request->getShipperAddressStateOrProvinceCode());
            }
            if ($this->configData['dest_type'] == 'RES') {
                $addressPart->addChild('ResidentialAddress');
            }
        }

        $servicePart = $shipmentPart->addChild('Service');
        $servicePart->addChild('Code', $request->getShippingMethod());
        $packagePart = $shipmentPart->addChild('Package');
        $packagePart->addChild('Description', substr(implode(' ', $itemsDesc), 0, 35));
        //empirical
        $packagePart->addChild('PackagingType')->addChild('Code', $request->getPackagingType());
        $packageWeight = $packagePart->addChild('PackageWeight');
        $packageWeight->addChild('Weight', $request->getPackageWeight());
        $packageWeight->addChild('UnitOfMeasurement')->addChild('Code', $weightUnits);

        // set dimensions
        if ($length || $width || $height) {
            $packageDimensions = $packagePart->addChild('Dimensions');
            $packageDimensions->addChild('UnitOfMeasurement')->addChild('Code', $dimensionsUnits);
            $packageDimensions->addChild('Length', $length);
            $packageDimensions->addChild('Width', $width);
            $packageDimensions->addChild('Height', $height);
        }

        // ups support reference number only for domestic service
        if ($this->isUSCountry($request->getRecipientAddressCountryCode())
            && $this->isUSCountry($request->getShipperAddressCountryCode())
        ) {
            if ($request->getReferenceData()) {
                $referenceData = $request->getReferenceData() . $request->getPackageId();
            } else {
                $referenceData = 'Order #' .
                    $request->getOrderShipment()->getOrder()->getIncrementId() .
                    ' P' .
                    $request->getPackageId();
            }
            $referencePart = $packagePart->addChild('ReferenceNumber');
            $referencePart->addChild('Code', '02');
            $referencePart->addChild('Value', $referenceData);
        }

        $deliveryConfirmation = $packageParams->getDeliveryConfirmation();
        if ($deliveryConfirmation) {
            /** @var $serviceOptionsNode Xml */
            $serviceOptionsNode = null;
            switch ($this->getDeliveryConfirmationLevel($request->getRecipientAddressCountryCode())) {
                case self::DELIVERY_CONFIRMATION_PACKAGE:
                    $serviceOptionsNode = $packagePart->addChild('PackageServiceOptions');
                    break;
                case self::DELIVERY_CONFIRMATION_SHIPMENT:
                    $serviceOptionsNode = $shipmentPart->addChild('ShipmentServiceOptions');
                    break;
                default:
                    break;
            }
            if ($serviceOptionsNode !== null) {
                $serviceOptionsNode->addChild(
                    'DeliveryConfirmation'
                )->addChild(
                    'DCISType',
                    $packageParams->getDeliveryConfirmation()
                );
            }
        }

        $shipmentPart->addChild('PaymentInformation')
            ->addChild('Prepaid')
            ->addChild('BillShipper')
            ->addChild('AccountNumber', $this->configData['shipper_number']);

        if ($request->getPackagingType() != $this->configHelper->getCode('container', 'ULE')
            && $request->getShipperAddressCountryCode() == self::USA_COUNTRY_ID
            && ($request->getRecipientAddressCountryCode() == 'CA'
                || $request->getRecipientAddressCountryCode() == 'PR')
        ) {
            $invoiceLineTotalPart = $shipmentPart->addChild('InvoiceLineTotal');
            $invoiceLineTotalPart->addChild('CurrencyCode', $request->getBaseCurrencyCode());
            $invoiceLineTotalPart->addChild('MonetaryValue', ceil($packageParams->getCustomsValue()));
        }

        $labelPart = $xmlRequest->addChild('LabelSpecification');
        $labelPart->addChild('LabelPrintMethod')->addChild('Code', 'GIF');
        $labelPart->addChild('LabelImageFormat')->addChild('Code', 'GIF');

        $userId = $this->configData['username'];
        $userIdPass = $this->configData['password'];
        $accessKey = $this->configData['access_license_number'];
        $header = <<<XMLAuth
<?xml version="1.0" ?>
<AccessRequest xml:lang="en-US">
    <AccessLicenseNumber>$accessKey</AccessLicenseNumber>
    <UserId>$userId</UserId>
    <Password>$userIdPass</Password>
</AccessRequest>
XMLAuth;

        return $header .$xmlRequest->asXML();
    }

    /**
     * Get quotes for requests.
     *
     * @param Request[] $requests
     * @throws ShipmentCreationException
     * @return Xml[]
     */
    protected abstract function requestQuotes(array $requests): array;

    /**
     * Prepare XML to accept shipment.
     *
     * @param Xml $quote
     * @return string
     */
    protected final function prepareAcceptXmlRequest(Xml $quote): string
    {
        /** @var Xml $xmlRequest */
        $xmlRequest = $this->xmlFactory->create(
            ['data' => '<?xml version = "1.0" ?><ShipmentAcceptRequest/>']
        );
        $request = $xmlRequest->addChild('Request');
        $request->addChild('RequestAction', 'ShipAccept');
        $xmlRequest->addChild('ShipmentDigest', $quote->ShipmentDigest);


        $userId = $this->configData['username'];
        $userIdPass = $this->configData['password'];
        $accessKey = $this->configData['access_license_number'];
        $header = <<<XMLAuth
<?xml version="1.0" ?>
<AccessRequest xml:lang="en-US">
  <AccessLicenseNumber>$accessKey</AccessLicenseNumber>
  <UserId>$userId</UserId>
  <Password>$userIdPass</Password>
</AccessRequest>
XMLAuth;

        return $header .$xmlRequest->asXML();
    }

    /**
     * Send acceptance for quotes.
     *
     * @param Xml[] $quotes
     * @return CreatedShipment[]
     */
    protected abstract function sendAcceptance(array $quotes): array;

    /**
     * Confirmation URL.
     *
     * @return string
     */
    protected final function getConfirmUrl(): string
    {
        return $this->configData['confirm_url'];
    }

    /**
     * Accept URL.
     *
     * @return string
     */
    protected final function getAcceptUrl(): string
    {
        return $this->configData['accept_url'];
    }

    /**
     * @inheritDoc
     */
    public final function create(array $requests): array
    {
        return $this->sendAcceptance($this->requestQuotes($requests));
    }
}
