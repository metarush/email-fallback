<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

class Builder extends Config
{
    /**
     * Array of Server objects
     *
     * @var array
     */
    private $servers;

    public function __construct(array $servers)
    {
        $this->servers = $servers;
    }

    public function build(): Emailer
    {
        $cfg = (new Config)
            ->addServers($this->servers)
            ->setAdminEmail($this->getAdminEmail())
            ->setFromEmail($this->getFromEmail())
            ->setAppName($this->getAppName());

        return new Emailer($cfg);
    }
}
