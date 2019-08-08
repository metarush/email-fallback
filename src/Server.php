<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

/**
 * A class used to define an SMTP server
 *
 * This is a dependency in the Config class
 */
class Server
{
    private $host;
    private $user;
    private $pass;
    private $encr;
    private $port;

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass)
    {
        $this->pass = $pass;

        return $this;
    }

    public function getEncr(): ?string
    {
        return $this->encr;
    }

    public function setEncr(string $encr)
    {
        $this->encr = $encr;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }
}
