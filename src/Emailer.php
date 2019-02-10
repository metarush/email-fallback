<?php

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

    public function __construct(Config $cfg)
    {
        parent::__construct(true); // true, turns on exceptions in PHPMailer

        $this->cfg = $cfg;

        // PHPMailer stuff
        $this->SMTPAuth = true;
        $this->isSMTP();
    }

    /**
     * Send email using servers defined in Config and fallback to them
     *
     * @param int $serverKey
     * @return int The sucessful server key
     * @throws Error
     * @throws Exception
     */
    public function sendEmailFallback(int $serverKey = 0): int
    {
        $servers = $this->cfg->getServers();

        $serverCount = count($servers);
        if ($serverCount < 1)
            throw new Error('At least 1 SMTP server must be defined');

        if (!key_exists($serverKey, $servers))
            throw new Error('Server key doesn\'t exist');

        for ($i = $serverKey; $i < $serverCount; $i++)
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

        $this->From = $this->cfg->getFromEmail();
        $this->clearAllRecipients();
        $this->addAddress($this->cfg->getAdminEmail());
        $this->Subject = $this->cfg->getAppName() . ': SMTP server(s) failed';

        $this->Body = $msg;

        // send using the last set SMTP server
        $this->send();
    }
}
