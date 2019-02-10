<?php

use MetaRush\EmailFallback;

require_once __DIR__ . '/Common.php';

class EmailerTest extends Common
{

    public function testSendEmailUsing1Server()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort($_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0'])
        ];

        $cfg = (new EmailFallback\Config)
            ->addServers($servers)
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($cfg);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(0, $serverKey);
    }

    public function testSendEmailUsingFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('invalid')
                ->setPass('invalid')
                ->setPort('123')
                ->setEncr('invalid'),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort($_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1'])
        ];

        $this->cfg = (new EmailFallback\Config)
            ->addServers($servers)
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($this->cfg);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(1, $serverKey);
    }

    public function testSendEmailUsingSpecificServer()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort($_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0']),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort($_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1'])
        ];

        $this->cfg = (new EmailFallback\Config)
            ->addServers($servers)
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($this->cfg);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $serverKey = $mailer->sendEmailFallback(1); // define server key to use

        $this->assertEquals(1, $serverKey);
    }

    public function testSendUsingSpecificServerThenFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort($_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1']),
            1 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('deliberateInvalidHost')
                ->setPass('deliberateInvalidHost')
                ->setPort('123')
                ->setEncr('deliberateInvalidHost'),
            2 => (new EmailFallback\Server)
                ->setHost('anotherInvalidHost')
                ->setUser('anotherInvalidHost')
                ->setPass('anotherInvalidHost')
                ->setPort('123')
                ->setEncr('anotherInvalidHost')
        ];

        $this->cfg = (new EmailFallback\Config)
            ->addServers($servers)
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($this->cfg);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $serverKey = $mailer->sendEmailFallback(1); // define server key to use

        $this->assertEquals(0, $serverKey);
    }

    public function testSendingWithAllFailedServers()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('deliberateInvalidHost')
                ->setUser('deliberateInvalidHost')
                ->setPass('deliberateInvalidHost')
                ->setPort('123')
                ->setEncr('deliberateInvalidHost'),
            1 => (new EmailFallback\Server)
                ->setHost('AnotherFailedHost')
                ->setUser('AnotherFailedHost')
                ->setPass('AnotherFailedHost')
                ->setPort('123')
                ->setEncr('AnotherFailedHost'),
            2 => (new EmailFallback\Server)
                ->setHost('anotherInvalidHost')
                ->setUser('anotherInvalidHost')
                ->setPass('anotherInvalidHost')
                ->setPort('123')
                ->setEncr('anotherInvalidHost')
        ];

        $this->cfg = (new EmailFallback\Config)
            ->addServers($servers)
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME']);

        $mailer = new EmailFallback\Emailer($this->cfg);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $this->expectException(EmailFallback\Exception::class);

        $mailer->sendEmailFallback();
    }
}
