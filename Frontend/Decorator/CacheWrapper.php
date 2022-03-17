<?php

declare(strict_types=1);

namespace Guentur\CacheWrapper\Frontend\Decorator;

use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class CacheWrapper extends TagScope
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
     * @param \Magento\Framework\Cache\FrontendInterface $frontend
     * @param $tag
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Cache\FrontendInterface $frontend,
                                                   $tag,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        parent::__construct($frontend, $tag);
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param string $index
     * @param callable $data
     * @param array $cacheTags
     * @return array
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
    public function getDataFromCache(string $index)
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

    public function cleanWithMode(string $mode, array $tags = [])
    {
        $this->getFrontend()->clean($mode, $tags);
    }
}
