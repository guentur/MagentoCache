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
     * @param int $lifeTime
     * @return mixed
     */
    public function getCached(string $index, callable $data, array $cacheTags = [], $lifeTime = null);

    /**
     * @param string $index
     * @return mixed
     */
    public function getDataFromCache(string $index);

    /**
     * @param string $index
     * @param array $data
     * @param array $cacheTags
     * @param int $lifeTime
     * @return mixed
     */
    public function saveToCache(string $index, array $data, array $cacheTags = [], $lifeTime = null);

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
     * @return mixed
     */
    public function cleanWithMode(string $mode, array $tags = []);
}
