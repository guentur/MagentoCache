<?php

declare(strict_types=1);

namespace Guentur\CacheWrapper\App;

use Magento\Framework\App\CacheInterface as FrameworkCacheInterface;

/**
 * @api
 */
interface CacheInterface extends FrameworkCacheInterface
{
    /**
     * @param string $index
     * @param callable $data
     * @param array $cacheTags
     * @return mixed
     */
    public function getCached(string $index, callable $data, array $cacheTags = []);

    /**
     * @param string $index
     * @return mixed
     */
    public function getDataFromCache(string $index);

    /**
     * @param string $index
     * @param array $data
     * @param array $cacheTags
     * @return mixed
     */
    public function saveToCache(string $index, array $data, array $cacheTags = []);

    /**
     * @param string $mode
     * @param array $tags
     * @return mixed
     */
    public function cleanWithMode(string $mode, array $tags = []);
}
