<?php

declare(strict_types=1);

namespace Guentur\CacheWrapper\App\Data;

/**
 * @api
 */
interface CacheInfoInterface
{
    /**
     * @return callable
     */
    public function getData(): callable;

    /**
     * @param callable $data
     * @return void
     */
    public function setData(callable $data): void;

    /**
     * @return callable
     */
    public function getIndex(): string;

    /**
     * @param string $index
     * @return void
     */
    public function setIndex(string $index): void;

    /**
     * @return array
     */
    public function getTags(): array;

    /**
     * @param array $tags
     * @return void
     */
    public function setTags(array $tags): void;

    /**
     * @return int
     */
    public function getLifetime(): int;

    /**
     * @param int $lifetime
     * @return void
     */
    public function setLifetime(int $lifetime): void;

}
