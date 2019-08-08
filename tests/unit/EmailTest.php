<?php

declare(strict_types=1);

use MetaRush\EmailFallback;

require_once __DIR__ . '/Common.php';

/**
 * @runTestsInSeparateProcesses
 */
class EmailerTest extends Common
{

    public function testSendEmailUsing1Server()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0'])
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(0, $serverKey);
    }

    public function testSendEmailUsingFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('invalid')
                ->setPass('invalid')
                ->setPort(123)
                ->setEncr('invalid'),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1'])
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(1, $serverKey);
    }

    public function testSendEmailUsingSpecificServer()
    {
        \sleep(10); // allowance for Mailtrap's limit

        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0']),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1'])
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $serverKey = $mailer->sendEmailFallback(1); // define server key to use

        $this->assertEquals(1, $serverKey);
    }

    public function testSendUsingSpecificServerThenFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1']),
            1 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('deliberateInvalidHost')
                ->setPass('deliberateInvalidHost')
                ->setPort(123)
                ->setEncr('deliberateInvalidHost'),
            2 => (new EmailFallback\Server)
                ->setHost('anotherInvalidHost')
                ->setUser('anotherInvalidHost')
                ->setPass('anotherInvalidHost')
                ->setPort(123)
                ->setEncr('anotherInvalidHost')
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $serverKey = $mailer->sendEmailFallback(1); // define server key to use

        $this->assertEquals(0, $serverKey);
    }

    public function testSendingWithAllFailedServers()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('deliberateInvalidHost')
                ->setPass('deliberateInvalidHost')
                ->setPort(123)
                ->setEncr('deliberateInvalidHost'),
            1 => (new EmailFallback\Server)
                ->setHost('AnotherFailedHost')
                ->setUser('AnotherFailedHost')
                ->setPass('AnotherFailedHost')
                ->setPort(123)
                ->setEncr('AnotherFailedHost'),
            2 => (new EmailFallback\Server)
                ->setHost('anotherInvalidHost')
                ->setUser('anotherInvalidHost')
                ->setPass('anotherInvalidHost')
                ->setPort(123)
                ->setEncr('anotherInvalidHost')
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $this->expectException(EmailFallback\Exception::class);

        $mailer->sendEmailFallback();
    }

    public function testRoundRobinWithFilesDriver()
    {
        // only test if user wants to test files driver
        if ($_ENV['MREF_FILES_ENABLED'] != 1)
            return;

        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0']),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1']),
        ];

        $path = ($_ENV['MREF_FILES_PATH'] != '') ?
            $_ENV['MREF_FILES_PATH'] : __DIR__ . '/cache_data/';

        $driverConfig = [
            'path' => $path
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->setRoundRobinMode(true)
            ->setRoundRobinDriver('files')
            ->setRoundRobinDriverConfig($driverConfig);

        $repo = new EmailFallback\Repo($cfg->getRoundRobinDriver(), $cfg->getRoundRobinDriverConfig());

        // seed
        $repo->setLastServer(0);

        // ----------------------------------------------
        // send 1st email
        // ----------------------------------------------
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(1, $repo->getLastServer());

        // ----------------------------------------------
        // send 2nd email
        // ----------------------------------------------
        $cfg->setSubject('Test inquiry 2');
        $cfg->setBody('Test Body 2');
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(0, $repo->getLastServer());
    }

    public function testRoundRobinWithMemcachedDriver()
    {
        // only test if user wants to test memcached driver
        if ($_ENV['MREF_MEMCACHED_ENABLED'] != 1)
            return;

        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0']),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1']),
        ];

        $driverConfig = [
            'host' => $_ENV['MREF_MEMCACHED_HOST'],
            'port' => (int) $_ENV['MREF_MEMCACHED_PORT'],
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->setRoundRobinMode(true)
            ->setRoundRobinDriver('memcached')
            ->setRoundRobinDriverConfig($driverConfig);

        $repo = new EmailFallback\Repo($cfg->getRoundRobinDriver(), $cfg->getRoundRobinDriverConfig());

        // seed
        $repo->setLastServer(0);

        // ----------------------------------------------
        // send 1st email
        // ----------------------------------------------
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(1, $repo->getLastServer());

        // ----------------------------------------------
        // send 2nd email
        // ----------------------------------------------
        $cfg->setSubject('Test inquiry 2');
        $cfg->setBody('Test Body 2');
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(0, $repo->getLastServer());
    }

    public function testRoundRobinWithRedisDriver()
    {
        // only test if user wants to test redis driver
        if ($_ENV['MREF_REDIS_ENABLED'] != 1)
            return;

        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0']),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort((int) $_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1']),
        ];

        $driverConfig = [
            'host'     => $_ENV['MREF_REDIS_HOST'],
            'port'     => (int) $_ENV['MREF_REDIS_PORT'],
            'password' => $_ENV['MREF_REDIS_PASS'],
            'database' => (int) $_ENV['MREF_REDIS_DB']
        ];

        $cfg = (new EmailFallback\Config)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->setRoundRobinMode(true)
            ->setRoundRobinDriver('redis')
            ->setRoundRobinDriverConfig($driverConfig);

        $repo = new EmailFallback\Repo($cfg->getRoundRobinDriver(), $cfg->getRoundRobinDriverConfig());

        // seed
        $repo->setLastServer(0);

        // ----------------------------------------------
        // send 1st email
        // ----------------------------------------------
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(1, $repo->getLastServer());

        // ----------------------------------------------
        // send 2nd email
        // ----------------------------------------------
        $mailer = new EmailFallback\Emailer($cfg, $repo);

        $mailer->sendEmailFallback();
        $this->assertEquals(0, $repo->getLastServer());
    }
}
