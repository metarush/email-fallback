<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

class Builder extends Config
{

    /**
     * Return an instance of the EmailFallback class
     *
     * @return \MetaRush\EmailFallback\Emailer
     */
    public function build(): Emailer
    {
        // if not round-robin mode
        if (!$this->getRoundRobinMode())
            return new Emailer($this);

        // if round-robin mode
        $repo = new Repo($this->getRoundRobinDriver(), $this->getRoundRobinDriverConfig());
        return new Emailer($this, $repo);
    }
}
