<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidTypeException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;
use Phpfastcache\Drivers\Files\Config as FilesConfig;
use Phpfastcache\Drivers\Memcached\Config as MemcachedConfig;
use Phpfastcache\Drivers\Redis\Config as RedisConfig;
use Phpfastcache\CacheManager;

class Repo
{
    const LAST_SERVER = 'lastServer';

    /**
     * @var Psr16Adapter<mixed>
     */
    private $cache;

    /**
     *
     * @param string $driver
     * @param array<string, mixed> $driverConfig config options if applicable
     * @throws PhpfastcacheLogicException
     * @throws PhpfastcacheInvalidTypeException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheDriverCheckException
     */
    public function __construct(string $driver, array $driverConfig = [])
    {
        CacheManager::setDefaultConfig(new ConfigurationOption([]));

        $driverInstance = null;
        $driverName = '';

        if ($driver === 'files') {
            $driverInstance = new FilesConfig($driverConfig);
            $driverName = 'Files';
        } elseif ($driver === 'memcached') {
            $driverInstance = new MemcachedConfig($driverConfig);
            $driverName = 'Memcached';
        } elseif ($driver === 'redis') {
            $driverInstance = new RedisConfig($driverConfig);
            $driverName = 'Redis';
        }

        $newDriver = CacheManager::getInstance($driverName, $driverInstance);

        $this->cache = new Psr16Adapter($newDriver);
    }

    /**
     * Set the last server used to send email
     *
     * @param int $serverKey
     */
    public function setLastServer(int $serverKey): void
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
        /** @var int|null */
        $result = $this->cache->get(self::LAST_SERVER);
        return $result;
    }
}