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

        // ----------------------------------------------
        // translate config vars to PHPMailer
        // ----------------------------------------------

        $this->SMTPDebug = $cfg->getDebugLevel();

        $this->clearAllRecipients();

        $this->setFrom($cfg->getFromEmail(), $cfg->getFromName());

        $tos = $cfg->getTos();
        foreach ($tos as $to)
            $this->addAddress($to);

        $ccs = $cfg->getCcs();
        foreach ($ccs as $cc)
            $this->addCC($cc);

        $bccs = $cfg->getBccs();
        foreach ($bccs as $bcc)
            $this->addBCC($bcc);

        $repyTos = $cfg->getReplyTos();
        foreach ($repyTos as $replyTo)
            $this->addReplyTo($replyTo);

        $attachments = $cfg->getAttachments();
        foreach ($attachments as $attachment)
            $this->addAttachment($attachment);

        $customHeaders = $cfg->getCustomHeaders();
        foreach ($customHeaders as $k => $v)
            $this->addCustomHeader($k, $v);

        $this->Subject = $cfg->getSubject();
        $this->Body = $cfg->getBody();
    }

    /**
     * Send email with fallback using servers defined in Config. If $serverKey
     * is set but value is not defined in config, server key 0 will be used
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
            throw new Error('Server key ' . $serverKey . ' doesn\'t exist');

        for ($i = $serverKey; $i < $this->serverCount; $i++)
            $this->sendByServerKey($i);

        // if $serverKey != 0, still use the skipped servers as fallback
        for ($i = 0; $i < $serverKey; $i++)
            $this->sendByServerKey($i);

        // if all server failed, throw Exception
        if (!isset($this->successServerHost))
            throw new Exception('All SMTP servers failed. Additional info: '.$this->ErrorInfo);

        // if only some server failed, notify admin
        if (count($this->failedServers) > 0)
            $this->notifyAdmin();

        return $this->successServerKey;
    }

    /**
     * Determine which SMTP host to use based on failover settings or parameter
     *
     * If $serverKey is set but value not defined in config, server key 0 will be used
     *
     * @param int|null $serverKey
     * @return int|null|int
     */
    private function getServerToUse(?int $serverKey = null)
    {
        // if key is set, make sure it's defined in config, if not use 0
        if (!is_null($serverKey)) {
            $serverKey = ($serverKey >= $this->serverCount) ? 0 : $serverKey;
            return $serverKey;
        }

        // if round-robin is disabled, use first server
        if (!$this->cfg->getRoundRobinMode())
            return 0;

        // if first time to send, use first server
        if (is_null($this->repo->getLastServer()))
            return 0;

        // use next server, if next server is last or greater than config,
        // go back to first server 0
        $nextServer = $this->repo->getLastServer() + 1;
        return ($nextServer >= $this->serverCount) ? 0 : $nextServer;
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
        if (!$this->cfg->getAdminEmails())
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

        $adminEmails = $this->cfg->getAdminEmails();
        foreach ($adminEmails as $adminEmail)
            $this->addAddress($adminEmail);
        $this->Subject = $this->cfg->getAppName() . ': SMTP server(s) failed';

        $this->Body = $msg;

        // send using the last set SMTP server
        $this->send();
    }
}
