<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

use Magento\Shipping\Model\Simplexml\Element as Xml;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory as XmlFactory;
use Magento\Ups\Helper\Config as ConfigHelper;

/**
 * @inheritdoc
 */
class SyncShipmentCreator extends AbstractShipmentCreator
{
    /**
     * @var ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var XmlFactory
     */
    private $xmlFactory;

    /**
     * @inheritDoc
     */
    public function __construct(
        XmlFactory $xmlFactory,
        array $configData,
        ConfigHelper $configHelper,
        ClientFactory $clientFactory
    ) {
        parent::__construct($xmlFactory, $configData, $configHelper);
        $this->xmlFactory = $xmlFactory;
        $this->httpClientFactory = $clientFactory;
    }

    /**
     * @inheritDoc
     */
    protected function requestQuotes(array $requests): array
    {
        /** @var Xml[] $xmls */
        $xmls = [];
        /** @var string[] $errors */
        $errors = [];
        foreach ($requests as $request) {
            $requestXml = $this->prepareQuoteXmlRequest($request);
            $client = $this->httpClientFactory->create();
            try {
                $client->post($this->getConfirmUrl(), $requestXml);
                $responseXml = $this->xmlFactory->create(['data' => $client->getBody()]);
                if (isset($responseXml->Response->Error)
                    && in_array($responseXml->Response->Error->ErrorSeverity, ['Hard', 'Transient'])
                ) {
                    $errors[] = (string)$responseXml->Response->Error->ErrorDescription;
                    continue;
                }
                $xmls[] = $responseXml;
            } catch (\Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if ($errors) {
            throw new ShipmentCreationException($errors);
        }

        return $xmls;
    }

    /**
     * @inheritDoc
     */
    protected function sendAcceptance(array $quotes): array
    {
        /** @var Xml[] $xmls */
        $xmls = [];
        /** @var string[] $errors */
        $errors = [];
        foreach ($quotes as $quote) {
            $client = $this->httpClientFactory->create();
            try {
                $client->post($this->getAcceptUrl(), $this->prepareAcceptXmlRequest($quote));
                $responseXml = $this->xmlFactory->create(['data' => $client->getBody()]);
                if (isset($response->Error)) {
                    $errors[] = (string)$responseXml->Error->ErrorDescription;
                    continue;
                }
                $xmls[] = $responseXml;
            } catch (\Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if ($errors) {
            throw new ShipmentCreationException($errors);
        }
        /** @var CreatedShipment[] $created */
        $created = [];
        foreach ($xmls as $xml) {
            $shippingLabelContent = (string)$xml->ShipmentResults->PackageResults->LabelImage->GraphicImage;
            $trackingNumber = (string)$xml->ShipmentResults->PackageResults->TrackingNumber;
            $created[] = new CreatedShipment($trackingNumber, base64_decode($shippingLabelContent));
        }

        return $created;
    }
}
