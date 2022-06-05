<?php

declare(strict_types=1);

namespace Guentur\CacheWrapper\Model\Cache;

use Psr\Log\LoggerInterface;
use Guentur\CacheWrapper\App\Data\CacheInfoInterface;

class CacheDataInfo implements CacheInfoInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var callable
     */
    private $data;

    /**
     * @var string
     */
    private $index;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var int
     */
    private $lifetime;

//    /**
//     * @param ObjectManagerInterface $objectManager
//     * @param SerializerInterface $serializer
//     * @param LoggerInterface $logger
//     */
//    public function __construct(
//        ObjectManagerInterface $objectManager,
//        SerializerInterface $serializer,
//        LoggerInterface $logger
//    ) {
//        parent::__construct($objectManager);
//        $this->serializer = $serializer;
//        $this->logger = $logger;
//    }

    /**
     * @inheritDoc
     */
    public function getData(): callable
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setData(callable $data): void
    {
        // TODO: Implement setData() method.
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): string
    {
        // TODO: Implement getIndex() method.`
    }

    /**
     * @inheritDoc
     */
    public function setIndex(string $index): void
    {
        // TODO: Implement setIndex() method.
    }

    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        // TODO: Implement getTags() method.
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags): void
    {
        // TODO: Implement setTags() method.
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): int
    {
        // TODO: Implement getLifetime() method.
    }

    /**
     * @inheritDoc
     */
    public function setLifetime(int $lifetime): void
    {
        // TODO: Implement setLifetime() method.
    }


}
