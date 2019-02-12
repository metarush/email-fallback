<?php

declare(strict_types=1);

use MetaRush\EmailFallback;

require_once __DIR__ . '/Common.php';

class RepoTest extends Common
{

    public function testFilesDriver()
    {
        $path = ($_ENV['MREF_FILES_PATH'] != '') ?
            $_ENV['MREF_FILES_PATH'] : __DIR__ . '/cache_data/';

        $driverConfig = [
            'path' => $path
        ];

        $repo = new EmailFallback\Repo('files', $driverConfig);

        // seed
        $repo->setLastServer(111);
        $this->assertEquals(111, $repo->getLastServer());
    }

    public function testMemcachedDriver()
    {
        $driverConfig = [
            'host'         => $_ENV['MREF_MEMCACHED_HOST'],
            'port'         => (int) $_ENV['MREF_MEMCACHED_PORT'],
            'saslUser'     => $_ENV['MREF_MEMCACHED_SASL_USER'],
            'saslPassword' => $_ENV['MREF_MEMCACHED_SASL_PASS']
        ];

        $repo = new EmailFallback\Repo('memcached', $driverConfig);

        // seed
        $repo->setLastServer(333);
        $this->assertEquals(333, $repo->getLastServer());
    }

    public function testRedisDriver()
    {
        $driverConfig = [
            'host'     => $_ENV['MREF_REDIS_HOST'],
            'port'     => (int) $_ENV['MREF_REDIS_PORT'],
            'password' => $_ENV['MREF_REDIS_PASS'],
            'database' => (int) $_ENV['MREF_REDIS_DB']
        ];

        $repo = new EmailFallback\Repo('redis', $driverConfig);

        // seed
        $repo->setLastServer(555);

        $this->assertEquals(555, $repo->getLastServer());
    }
}
