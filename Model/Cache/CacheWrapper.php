<?php

declare(strict_types=1);

namespace Guentur\CacheWrapper\Model\Cache;

use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\ObjectManagerInterface;
use Guentur\CacheWrapper\App\CacheInterface as CacheWrapperInterface;

class CacheWrapper extends \Magento\Framework\App\Cache\Proxy implements CacheWrapperInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        parent::__construct($objectManager);
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param string $index
     * @param callable $data
     * @param array $cacheTags
     * @return array
     * @throws \Zend_Cache_Exception
     */
    public function getCached(string $index, callable $data, array $cacheTags = []): array
    {
        $cachedData = $this->getDataFromCache($index);

        if (!$cachedData) {
            $cachedData = $data();
            $this->saveToCache($index, $cachedData, $cacheTags);
        }

        return $cachedData;
    }

    /**
     * @param string $index
     * @return array|null
     */
    public function getDataFromCache(string $index): ?array
    {
        $result = null;
        $serializedData = $this->load($index);

        try {
            if ($serializedData) {
                $result = $this->serializer->unserialize($serializedData);
            }
        } catch (\InvalidArgumentException $e) {
            $this->logger->error($e->getMessage() . $e->getTraceAsString());
        }
        return $result;
    }

    /**
     * @param string $index
     * @param array $data
     * @param array $cacheTags
     * @return void
     * @throws \Zend_Cache_Exception
     */
    public function saveToCache(string $index, array $data, array $cacheTags = []): void
    {
        $this->cleanWithMode(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [$index]);
        $cacheTags[] = $index;
        try {
            $result = $this->save(
                $this->serializer->serialize($data),
                $index,
                $cacheTags
            );
            if (!$result) {
                throw new \OutOfRangeException(__("Cache with index $index is not saved"));
            }
        } catch (\OutOfRangeException $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }
    }

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     * @throws \Zend_Cache_Exception
     */
    public function cleanWithMode(string $mode, array $tags = []): bool
    {
        return $this->getFrontend()->clean($mode, $tags);
    }
}
