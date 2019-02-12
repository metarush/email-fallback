<?php

declare(strict_types=1);

namespace MetaRush\EmailFallback;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * This is the main emailer class
 */
class Emailer extends PHPMailer
{
    private $cfg;
    private $failedServers = [];
    private $successServerHost = null;
    private $successServerKey = null;
    private $repo;
    private $serverCount;

    public function __construct(Config $cfg, ?Repo $repo = null)
    {
        parent::__construct(true); // true, turns on exceptions in PHPMailer

        $this->cfg = $cfg;

        if ($cfg->getRoundRobinMode())
            $this->repo = $repo;

        // PHPMailer stuff
        $this->SMTPAuth = true;
        $this->isSMTP();
    }

    /**
     * Send email using servers defined in Config then fallback to them
     *
     * @param int|null $serverKey
     * @return int
     * @throws Error
     * @throws Exception
     */
    public function sendEmailFallback(?int $serverKey = null): int
    {
        $servers = $this->cfg->getServers();

        $this->serverCount = count($servers);
        if ($this->serverCount < 1)
            throw new Error('At least 1 SMTP server must be defined');

        // get server key to use (consider if round-robin is enabled)
        $serverKey = $this->getServerToUse($serverKey);

        if (!key_exists($serverKey, $servers))
            throw new Error('Server key doesn\'t exist');

        for ($i = $serverKey; $i < $this->serverCount; $i++)
            $this->sendByServerKey($i);

        // if $serverKey != 0, still use the skipped servers as fallback
        for ($i = 0; $i < $serverKey; $i++)
            $this->sendByServerKey($i);

        // if all server failed, throw Exception
        if (!isset($this->successServerHost))
            throw new Exception('All SMTP servers failed');

        // if only some server failed, notify admin
        if (count($this->failedServers) > 0)
            $this->notifyAdmin();

        return $this->successServerKey;
    }

    private function getServerToUse(?int $serverKey = null)
    {
        // send specific key if set
        if (!is_null($serverKey))
            return $serverKey;

        // if round-robin is disabled, use first server
        if (!$this->cfg->getRoundRobinMode())
            return 0;

        // if first time to send, use first server
        if (is_null($this->repo->getLastServer()))
            return 0;

        // use next server, if next server is last, go back to first server (0)
        $nextServer = $this->repo->getLastServer() + 1;
        return ($this->serverCount === $nextServer) ? 0 : $nextServer;
    }

    /**
     * Send email by server key
     *
     * @param int $key
     * @return bool
     */
    private function sendByServerKey(int $key): bool
    {
        if (isset($this->successServerHost))
            return true;

        try {

            $servers = $this->cfg->getServers();

            $this->Host = $servers[$key]->getHost();
            $this->Username = $servers[$key]->getUser();
            $this->Password = $servers[$key]->getPass();
            $this->SMTPSecure = $servers[$key]->getEncr();
            $this->Port = $servers[$key]->getPort();
            $this->send();

            $this->successServerHost = $this->Host;
            $this->successServerKey = $key;

            if ($this->cfg->getRoundRobinMode())
                $this->repo->setLastServer($key);

            return true;

        } catch (\PHPMailer\PHPMailer\Exception $ex) {

            $this->failedServers[$key] = $this->Host . ': ' . $ex->getMessage();

            return false;
        }
    }

    /**
     * Notify admin about errors
     *
     * @return void
     */
    private function notifyAdmin(): void
    {
        if (!$this->cfg->getAdminEmail())
            return;

        $ex = new \Exception;
        $trace = $ex->getTraceAsString();

        // format msg
        $msg = "Trying to send email with subject: \"$this->Subject\" "
            . "triggered a failover.\r\n\r\n"
            . "Server(s) that failed:\r\n";
        foreach ($this->failedServers as $k => $v)
            $msg .= "- Server $k $v \r\n";
        $msg .= "\r\nTrace:\r\n$trace\r\n\r\n"
            . "Successful failover server:\r\n$this->Host\r\n\r\n"
            . "--\r\n"
            . 'Sent from ' . __FILE__ . ' using ' . $this->Host;

        $this->FromName = $this->cfg->getNotificationFromEmail();
        $this->From = $this->cfg->getNotificationFromEmail();
        $this->clearAllRecipients();
        $this->addAddress($this->cfg->getAdminEmail());
        $this->Subject = $this->cfg->getAppName() . ': SMTP server(s) failed';

        $this->Body = $msg;

        // send using the last set SMTP server
        $this->send();
    }
}
