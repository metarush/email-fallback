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
        $this->addServers($this->servers);

        // if not round-robin mode
        if (!$this->getRoundRobinMode())
            return new Emailer($this);

        // if round-robin mode
        $repo = new Repo($this->getRoundRobinDriver(), $this->getRoundRobinDriverConfig());
        return new Emailer($this, $repo);
    }
}
