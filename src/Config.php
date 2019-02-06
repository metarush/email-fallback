<?php

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
    private $fromEmail;

    /**
     * Optinally set name of app to easily identify it when error notifcations are sent
     *
     * @var string
     */
    private $appName;

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

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getAppName(): ?string
    {
        return $this->appName;
    }

    public function setAppName(string $appName)
    {
        $this->appName = $appName;

        return $this;
    }
}
