<?php
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Query;


use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\BatchRequestItem;
use Magento\Framework\GraphQl\Query\Resolver\BatchResolverInterface;
use Magento\Framework\GraphQl\Query\Resolver\BatchResponse;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class BatchResolverWrapper implements ResolverInterface
{
    /**
     * @var BatchResolverInterface
     */
    private $resolver;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var \Magento\Framework\GraphQl\Query\Resolver\ContextInterface|null
     */
    private $context;

    /**
     * @var Field|null
     */
    private $field;

    /**
     * @var BatchRequestItem[]
     */
    private $request = [];

    /**
     * @var BatchResponse|null
     */
    private $response;

    /**
     * BatchResolverWrapper constructor.
     * @param BatchResolverInterface $resolver
     * @param ValueFactory $valueFactory
     */
    public function __construct(BatchResolverInterface $resolver, ValueFactory $valueFactory)
    {
        $this->resolver = $resolver;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param BatchRequestItem $item
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     */
    private function resolveFor(BatchRequestItem $item)
    {
        if ($this->request) {
            $this->response = $this->resolver->resolve($this->context, $this->field, $this->request);

            $this->request = [];
            $this->context = null;
            $this->field = null;
        }

        return $this->response->findResponseFor($item);
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $item = new BatchRequestItem($info, $value, $args);
        $this->request[] = $item;
        $this->context = $context;
        $this->field = $field;

        return $this->valueFactory->create(
            function () use ($item) {
                return $this->resolveFor($item);
            }
        );
    }
}
