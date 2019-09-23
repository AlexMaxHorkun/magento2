<?php
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Query\Resolver;


use Magento\Framework\GraphQl\Config\Element\Field;

interface BatchResolverInterface
{
    /**
     * @param ContextInterface $context
     * @param Field $field
     * @param BatchRequestItem[] $requests
     * @return BatchResponse
     * @throws \Throwable
     */
    public function resolve(ContextInterface $context, Field $field, array $requests): BatchResponse;
}
