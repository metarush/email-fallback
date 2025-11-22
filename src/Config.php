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
     * Optionally set admin emails to get error notifications
     *
     * @var ?array
     */
    private $adminEmails;

    /**
     * If you set an admin email, you must set a from email for error notifications
     *
     * @var string
     */
    private $notificationFromEmail = null;

    /**
     * Optionally set name of app to easily identify it when error notifications are sent
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
     * @var string|null
     */
    private $roundRobinDriver = null;

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

    /**
     * Name of From address
     *
     * @var string
     */
    private $fromName = null;

    /**
     * From email
     *
     * @var string
     */
    private $fromEmail;

    /**
     * Array of Tos
     *
     * @var array
     */
    private $tos;

    /**
     * Array of CCs
     *
     * @var array
     */
    private $ccs = [];

    /**
     * Array of BCCs
     *
     * @var array
     */
    private $bccs = [];

    /**
     * Email subject
     *
     * @var string
     */
    private $subject;

    /**
     * Email body
     *
     * @var string
     */
    private $body;

    /**
     * Array of reply-to emails
     *
     * @var array
     */
    private $replyTos = [];

    /**
     * Array of attachments
     *
     * @var ?array
     */
    private $attachments = [];

    /**
     * PHPMailer debug level
     *
     * https://github.com/PHPMailer/PHPMailer/wiki/SMTP-Debugging
     *
     * @var int
     */
    private $debugLevel = 0;

    /**
     * Array of custom headers
     *
     * @var ?array
     */
    private $customHeaders = [];

    /**
     *
     * @var string
     */
    private $charSet = 'UTF-8';

    public function getDebugLevel(): int
    {
        return $this->debugLevel;
    }

    public function setDebugLevel(int $debugLevel)
    {
        $this->debugLevel = $debugLevel;
        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    public function getReplyTos(): array
    {
        return $this->replyTos;
    }

    public function setReplyTos(array $replyTos)
    {
        $this->replyTos = $replyTos;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getBccs(): array
    {
        return $this->bccs;
    }

    public function setBccs(array $bccs)
    {
        $this->bccs = $bccs;
        return $this;
    }

    public function getCcs(): array
    {
        return $this->ccs;
    }

    public function setCcs(array $ccs)
    {
        $this->ccs = $ccs;
        return $this;
    }

    public function getTos(): array
    {
        return $this->tos;
    }

    public function setTos(array $tos)
    {
        $this->tos = $tos;
        return $this;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(string $fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function getFromName(): string
    {
        return $this->fromName ?? $this->fromEmail;
    }

    public function setFromName(string $fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    public function getRoundRobinDriverConfig(): array
    {
        return $this->roundRobinDriverConfig;
    }

    public function setRoundRobinDriverConfig(array $roundRobinDriverConfig)
    {
        $this->roundRobinDriverConfig = $roundRobinDriverConfig;
        return $this;
    }

    public function getRoundRobinDriver(): ?string
    {
        return $this->roundRobinDriver;
    }

    public function setRoundRobinDriver(?string $roundRobinDriver)
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

    public function setServers(array $servers)
    {
        $this->servers = $servers;
        return $this;
    }

    public function getAdminEmails(): ?array
    {
        return $this->adminEmails;
    }

    public function setAdminEmails(array $adminEmails)
    {
        $this->adminEmails = $adminEmails;
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

    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    public function setCustomHeaders(array $customHeaders)
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    public function getCharSet(): string
    {
        return $this->charSet;
    }

    public function setCharSet(string $charSet)
    {
        $this->charSet = $charSet;
        return $this;
    }

}