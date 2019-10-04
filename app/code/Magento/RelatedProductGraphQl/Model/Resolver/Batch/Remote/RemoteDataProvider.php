<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote;

use Magento\Catalog\Model\LinkedProduct;
use Magento\Catalog\Model\Product\Link;
use Magento\Framework\HTTP\AsyncClient\Request;
use Magento\Framework\HTTP\AsyncClientInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\DeferredLinkedProductsFactory;

/**
 * Loads linked products remotely.
 */
class RemoteDataProvider
{
    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var AsyncClientInterface
     */
    private $asyncClient;

    /**
     * @var DeferredLinkedProductsFactory
     */
    private $deferredFactory;

    /**
     * RemoteDataProvider constructor.
     * @param StoreManager $storeManager
     * @param AsyncClientInterface $asyncClient
     * @param \Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\DeferredLinkedProductsFactory $deferredFactory
     */
    public function __construct(
        StoreManager $storeManager,
        AsyncClientInterface $asyncClient,
        \Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote\DeferredLinkedProductsFactory $deferredFactory
    ) {
        $this->storeManager = $storeManager;
        $this->asyncClient = $asyncClient;
        $this->deferredFactory = $deferredFactory;
    }

    /**
     * @throws \Throwable
     * @return string
     */
    private function getBaseURI(): string
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();

        return $store->getBaseUrl() .'rest/V1/';
    }

    /**
     * @param array $ids
     * @param int $linkType
     * @return DeferredLinkedProducts
     */
    public function findLinked(array $ids, int $linkType): DeferredLinkedProducts
    {
        $linkTypes = [
            Link::LINK_TYPE_RELATED => 'related',
            Link::LINK_TYPE_UPSELL => 'upsell',
            Link::LINK_TYPE_CROSSSELL => 'crosssell'
        ];
        $query = '?' .http_build_query(['ids' => $ids]);
        $url = $this->getBaseURI() .'products/links/' .$linkTypes[$linkType] .'/products' .$query;
        $response = $this->asyncClient->request(
            new Request(
                $url,
                Request::METHOD_GET,
                [
                    'Accept' => ['application/json']
                ],
                null
            )
        );

        return $this->deferredFactory->create(['apiResponse' => $response]);
    }
}
