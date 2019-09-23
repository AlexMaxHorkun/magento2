<?php
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Query\Resolver;


class BatchResponse
{
    /**
     * @var \SplObjectStorage
     */
    private $responses;

    /**
     * BatchResponse constructor.
     */
    public function __construct()
    {
        $this->responses = new \SplObjectStorage();
    }

    /**
     * @param BatchRequestItem $request
     * @param array|int|string|float|Value $response
     */
    public function addResponse(BatchRequestItem $request, $response): void
    {
        $this->responses[$request] = $response;
    }

    /**
     * @param BatchRequestItem $item
     * @return mixed|Value
     */
    public function findResponseFor(BatchRequestItem $item)
    {
        if (!$this->responses->offsetExists($item)) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        return $this->responses[$item];
    }
}
