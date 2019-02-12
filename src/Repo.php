<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\CacheManager;
use Phpfastcache\Drivers;

class Repo
{
    const LAST_SERVER = 'lastServer';
    private $cache;

    /**
     *
     * @param string $driver
     * @param array $driverConfig config options if applicablae
     */
    public function __construct(string $driver, array $driverConfig = [])
    {
        if ($driver === 'files')
            $newDriver = CacheManager::Files(new Drivers\Files\Config($driverConfig));

        if ($driver === 'memcached')
            $newDriver = CacheManager::Memcached(new Drivers\Memcached\Config($driverConfig));

        if ($driver === 'redis')
            $newDriver = CacheManager::Redis(new Drivers\Redis\Config($driverConfig));

        $this->cache = new Psr16Adapter($newDriver);
    }

    /**
     * Set the last server used to send email
     *
     * @param int $serverKey
     */
    public function setLastServer(int $serverKey)
    {
        $this->cache->set(self::LAST_SERVER, $serverKey, 2592000); // 30 days
    }

    /**
     * Get the last server used to send email
     *
     * @return int|null
     */
    public function getLastServer(): ?int
    {
        return $this->cache->get(self::LAST_SERVER);
    }
}
