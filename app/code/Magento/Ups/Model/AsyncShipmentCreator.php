<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Model;

use Magento\Framework\HTTP\AsyncClient\HttpResponseDeferredInterface;
use Magento\Framework\HTTP\AsyncClientInterface;
use Magento\Shipping\Model\Simplexml\Element as Xml;
use Magento\Shipping\Model\Simplexml\ElementFactory as XmlFactory;
use Magento\Ups\Helper\Config as ConfigHelper;
use Magento\Framework\HTTP\AsyncClient\Request as HttpRequest;

/**
 * Use async HTTP client to send requests.
 */
class AsyncShipmentCreator extends AbstractShipmentCreator
{
    /**
     * @var AsyncClientInterface
     */
    private $client;

    /**
     * @var XmlFactory
     */
    private $xmlFactory;

    /**
     * AsyncShipmentCreator constructor.
     * @param AsyncClientInterface $client
     * @param XmlFactory $xmlFactory
     * @param array $configData
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        AsyncClientInterface $client,
        XmlFactory $xmlFactory,
        array $configData,
        ConfigHelper $configHelper
    ) {
        $this->client = $client;
        $this->xmlFactory = $xmlFactory;
        parent::__construct($xmlFactory, $configData, $configHelper);
    }

    /**
     * @inheritdoc
     */
    protected function requestQuotes(array $requests): array
    {
        /** @var Xml[] $xmls */
        $xmls = [];
        //Sending requests
        /** @var HttpResponseDeferredInterface[] $responses */
        $responses = [];
        foreach ($requests as $request) {
            $requestXml = $this->prepareQuoteXmlRequest($request);
            $responses[] = $this->client->request(
                new HttpRequest(
                    $this->getConfirmUrl(),
                    HttpRequest::METHOD_POST,
                    [
                        'Content-Type' => 'application/xml'
                    ],
                    $requestXml
                )
            );
        }

        //Processing requests
        /** @var string[] $errors */
        $errors = [];
        foreach ($responses as $responseDeferred) {
            try {
                $response = $responseDeferred->get();
                $responseXml = $this->xmlFactory->create(['data' => $response->getBody()]);
                if (isset($responseXml->Response->Error)
                    && in_array($responseXml->Response->Error->ErrorSeverity, ['Hard', 'Transient'])
                ) {
                    $errors[] = (string)$responseXml->Response->Error->ErrorDescription;
                    continue;
                }
                if ($response->getStatusCode() >= 400) {
                    $errors[] = 'Failed to retrieve data from UPS';
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
     * @inheritdoc
     */
    protected function sendAcceptance(array $quotes): array
    {
        //Sending requests
        /** @var HttpResponseDeferredInterface[] $responses */
        $responses = [];
        foreach ($quotes as $quote) {
            $responses[] = $this->client->request(
                new HttpRequest(
                    $this->getAcceptUrl(),
                    HttpRequest::METHOD_POST,
                    [
                        'Content-Type' => 'application/xml'
                    ],
                    $this->prepareAcceptXmlRequest($quote)
                )
            );
        }

        //Processing requests
        /** @var Xml[] $xmls */
        $xmls = [];
        /** @var string[] $errors */
        $errors = [];
        foreach ($responses as $responseDeferred) {
            try {
                $response = $responseDeferred->get();
                $responseXml = $this->xmlFactory->create(['data' => $response->getBody()]);
                if (isset($response->Error)) {
                    $errors[] = (string)$responseXml->Error->ErrorDescription;
                    continue;
                }
                if ($response->getStatusCode() >= 400) {
                    $errors[] = 'Failed to retrieve data from UPS';
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
