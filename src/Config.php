<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

class Config
{
    /**
     * Array of Server objects
     *
     * @var array
     */
    private $servers;

    /**
     * Optinally set admin email to get error notifications
     *
     * @var string
     */
    private $adminEmail;

    /**
     * If you set an admin email, you must set a from email for error notications
     *
     * @var string
     */
    private $notificationFromEmail = null;

    /**
     * Optinally set name of app to easily identify it when error notifcations are sent
     *
     * @var string
     */
    private $appName;

    /**
     * Flag to round-robin servers
     *
     * @var bool
     */
    private $roundRobinMode = false;

    /**
     * If you setRoundRobinMode(true), set driver to track last server used
     *
     * @var string
     */
    private $roundRobinDriver;

    /**
     * Key-value pair of driver's config.
     *
     * "files" config sample:
     *
     * [
     *  'path' => '/var/www/example/EmailFallbackCache/'
     * ]
     *
     * "memcached" config sample:
     *
     * [
     *  'host' => '127.0.0.1',
     *  'port' => 11211,
     *  'sasl_user' => '',
     *  'sasl_password' => ''
     * ]
     *
     * "redis" config sample:
     *
     * [
     *  'host' => '127.0.0.1',
     *  'port' => 6379,
     *  'password' => '',
     *  'database' => 0
     * ]
     *
     * @var array
     */
    private $roundRobinDriverConfig = [];

    public function getRoundRobinDriverConfig(): array
    {
        return $this->roundRobinDriverConfig;
    }

    public function setRoundRobinDriverConfig(array $roundRobinDriverConfig)
    {
        $this->roundRobinDriverConfig = $roundRobinDriverConfig;

        return $this;
    }

    public function getRoundRobinDriver(): string
    {
        return $this->roundRobinDriver;
    }

    public function setRoundRobinDriver(string $roundRobinDriver)
    {
        $this->roundRobinDriver = $roundRobinDriver;

        return $this;
    }

    public function getRoundRobinMode(): bool
    {
        return $this->roundRobinMode;
    }

    public function setRoundRobinMode(bool $roundRobinMode)
    {
        $this->roundRobinMode = $roundRobinMode;

        return $this;
    }

    /**
     *
     * @return Server[]
     */
    public function getServers(): array
    {
        return $this->servers;
    }

    public function addServers(array $servers)
    {
        $this->servers = $servers;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->adminEmail;
    }

    public function setAdminEmail(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    public function getNotificationFromEmail(): ?string
    {
        return $this->notificationFromEmail;
    }

    public function setNotificationFromEmail(string $notificationFromEmail)
    {
        $this->notificationFromEmail = $notificationFromEmail;

        return $this;
    }

    public function getAppName(): ?string
    {
        return $this->appName;
    }

    public function setAppName(?string $appName = null)
    {
        $this->appName = $appName;

        return $this;
    }
}
