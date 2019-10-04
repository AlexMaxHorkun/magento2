<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\Sync;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\BatchRequestItem;
use Magento\Framework\GraphQl\Query\Resolver\BatchResolverInterface;
use Magento\Framework\GraphQl\Query\Resolver\BatchResponse;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\RemoteDataProvider;

abstract class AbstractLinkedProducts implements BatchResolverInterface
{
    /**
     * @var RemoteDataProvider
     */
    private $provider;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * AbstractLinkedProducts constructor.
     * @param RemoteDataProvider $provider
     * @param ValueFactory $valueFactory
     */
    public function __construct(RemoteDataProvider $provider, ValueFactory $valueFactory)
    {
        $this->provider = $provider;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ContextInterface $context, Field $field, array $requests): BatchResponse
    {
        $productIds = [];
        $response = new BatchResponse();
        /** @var \Magento\Framework\GraphQl\Query\Resolver\BatchRequestItem $request */
        foreach ($requests as $request) {
            //Gathering fields and relations to load.
            if (empty($request->getValue()['model'])) {
                throw new LocalizedException(__('"model" value should be specified'));
            }
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $product = $request->getValue()['model'];
            $productIds[] = (int)$product->getId();
        }
        $relations = $this->provider->findLinked($productIds, $this->getLinkType());
        //Waiting for HTTP response.
        $data = $relations->get();
        foreach ($requests as $request) {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $product = $request->getValue()['model'];
            $id =  (int)$product->getId();
            $values = [];
            foreach ($data as $linkedProduct) {
                if ($linkedProduct->getProductId() === $id) {
                    $linkedData = $linkedProduct->getLinked()->getData();
                    $linkedData['model'] = $linkedProduct->getLinked();
                    $values[] = $linkedData;
                }
            }
            $response->addResponse($request, $values);
        }

        return $response;
    }

    /**
     * @return int
     */
    abstract protected function getLinkType(): int;
}
