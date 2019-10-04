<?php


namespace Magento\RelatedProductGraphQl\Model\Resolver\Batch\Remote;


use Magento\Catalog\Model\LinkedProduct;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Async\CallbackDeferred;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\AsyncClient\HttpResponseDeferredInterface;

class DeferredLinkedProducts extends CallbackDeferred
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @inheritDoc
     */
    public function __construct(ProductFactory $factory, HttpResponseDeferredInterface $apiResponse)
    {
        $this->productFactory = $factory;
        parent::__construct(function () use ($apiResponse) { return $this->processResponse($apiResponse); });
    }

    /**
     * @param HttpResponseDeferredInterface $response
     * @return LinkedProduct[]
     * @throws \Throwable
     */
    private function processResponse(HttpResponseDeferredInterface $response): array
    {
        $result = $response->get();
        $data = json_decode($result->getBody(), true);
        if (array_key_exists('message', $data) || array_key_exists('messages', $data)) {
            throw new LocalizedException(__('API Error:' .PHP_EOL .$result->getBody() .PHP_EOL));
        }
        $linked = [];
        foreach ($data as $linkedData) {
            $id = (int)$linkedData['product_id'];
            /** @var Product $linkedProduct */
            $linkedProduct = $this->productFactory->create();
            $linkedProduct->setData($linkedData['linked']);
            $linkedProduct->setId((int)$linkedData['linked']['id']);
            $linked[] = new LinkedProduct($id, $linkedProduct);
        }

        return $linked;
    }

    /**
     * @inheritDoc
     * @return LinkedProduct[]
     */
    public function get()
    {
        return parent::get();
    }
}
