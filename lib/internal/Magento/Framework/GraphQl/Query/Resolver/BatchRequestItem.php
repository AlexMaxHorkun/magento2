<?php
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Query\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class BatchRequestItem
{
    /**
     * @var ResolveInfo
     */
    private $info;

    /**
     * @var array|null
     */
    private $value;

    /**
     * @var array|null
     */
    private $args;

    /**
     * BatchRequestItem constructor.
     * @param ResolveInfo $info
     * @param null|array $value
     * @param null|array $args
     */
    public function __construct(ResolveInfo $info, ?array $value, ?array $args)
    {
        $this->info = $info;
        $this->value = $value;
        $this->args = $args;
    }

    /**
     * @return ResolveInfo
     */
    public function getInfo(): ResolveInfo
    {
        return $this->info;
    }

    /**
     * @return array|null
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    /**
     * @return array|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }
}
