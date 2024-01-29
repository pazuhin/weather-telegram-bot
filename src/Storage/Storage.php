<?php

namespace App\Storage;

use Psr\Cache\CacheItemPoolInterface;

class Storage
{
    public const PREFIX_STEP = 'step_';

    public function __construct
    (
        private CacheItemPoolInterface $cache,
        private int $lifetime = 0
    ) { }

    public function hasData(string $id): bool
    {
        $key = $this->getKey($id);

        return $this->cache->hasItem($key);
    }

    public function clearData(string $id): void
    {
        $stepKey = $this->getKey($id);

        $this->cache->deleteItem($stepKey);
    }

    public function getCurrentStep(string $id): int
    {
        $key = $this->getKey($id);
        if (!$this->cache->hasItem($key)) {
            return 0;
        }

        $item = $this->cache->getItem($key);

        return (int) $item->get();
    }

    public function setCurrentStep(string $id, int $step): void
    {
        $key = $this->getKey($id);

        $item = $this->cache->getItem($key);
        $item->set($step);
        if ($this->lifetime > 0) {
            $item->expiresAfter($this->lifetime);
        }

        $this->cache->save($item);
    }

    private function getKey(string $id): string
    {
        return sprintf('%s%s', self::PREFIX_STEP, $id);
    }
}
