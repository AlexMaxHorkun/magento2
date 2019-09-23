<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\RelatedProductGraphQl\Model\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Link;
use Magento\Catalog\Model\Product\LinkFactory;

/**
 * Related Products Data Provider
 */
class RelatedProductDataProvider
{
    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @param LinkFactory $linkFactory
     */
    public function __construct(
        LinkFactory $linkFactory
    ) {
        $this->linkFactory = $linkFactory;
    }

    /**
     * Related Products Data
     *
     * @param Product $product
     * @param array $fields
     * @param int $linkType
     * @return array
     */
    public function getData(Product $product, array $fields, int $linkType): array
    {
        $relatedProducts = $this->getRelatedProducts($product, $fields, $linkType);

        $productsData = [];
        foreach ($relatedProducts as $relatedProduct) {
            $productData = $relatedProduct->getData();
            $productData['model'] = $relatedProduct;
            $productsData[] = $productData;
        }
        return $productsData;
    }

    /**
     * Get Related Products
     *
     * @param Product $product
     * @param array $fields
     * @param int $linkType
     * @return Product[]
     */
    private function getRelatedProducts(Product $product, array $fields, int $linkType): array
    {
        /** @var Link $link */
        $link = $this->linkFactory->create([ 'data' => [
            'link_type_id' => $linkType,
        ]]);

        $collection = $link->getProductCollection();
        $collection->setIsStrongMode();
        foreach ($fields as $field) {
            $collection->addAttributeToSelect($field);
        }
        $collection->setProduct($product);

        return $collection->getItems();
    }

    /**
     * @param array $productIds
     * @param int $linkType
     * @return string[][] keys - IDs, values - list of linked product IDs.
     */
    public function getRelations(array $productIds, int $linkType): array
    {
        /** @var Link $link */
        $link = $this->linkFactory->create([ 'data' => [
            'link_type_id' => $linkType,
        ]]);
        $collection = $link->getLinkCollection();
        $collection->addProductIdsFilter($productIds);
        $collection->addLinkTypeIdFilter();
        $map = [];
        /** @var Link $item */
        foreach ($collection as $item) {
            if (!array_key_exists($item->getProductId(), $map)) {
                $map[$item->getProductId()] = [];
            }
            $map[$item->getProductId()][] = $item->getLinkedProductId();
        }

        return $map;
    }
}
