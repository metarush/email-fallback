<?php

use MetaRush\EmailFallback;

require_once __DIR__ . '/Common.php';

/**
 * @runTestsInSeparateProcesses
 */
class BuilderTest extends Common
{

    public function testBuilderAndFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('invalidHost') // deliberate invalid host
                ->setUser('invalid')
                ->setPass('invalid')
                ->setPort('123')
                ->setEncr('invalid'),
            1 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort($_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0'])
        ];

        $path = ($_ENV['MREF_FILES_PATH'] != '') ?
            $_ENV['MREF_FILES_PATH'] : __DIR__ . '/cache_data/';

        $driverConfig = [
            'path' => $path
        ];

        $mailer = (new EmailFallback\Builder)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setNotificationFromEmail($_ENV['MREF_FROM_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->setRoundRobinMode(true)
            ->setRoundRobinDriver('files')
            ->setRoundRobinDriverConfig($driverConfig)
            ->setCharSet('UTF-8')
            ->build();

        $this->assertInstanceOf(EmailFallback\Emailer::class, $mailer);
        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(1, $serverKey);
    }

    public function testBuilderWithoutFallback()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost($_ENV['MREF_SMTP_HOST_0'])
                ->setUser($_ENV['MREF_SMTP_USER_0'])
                ->setPass($_ENV['MREF_SMTP_PASS_0'])
                ->setPort($_ENV['MREF_SMTP_PORT_0'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_0'])
        ];

        $path = ($_ENV['MREF_FILES_PATH'] != '') ?
            $_ENV['MREF_FILES_PATH'] : __DIR__ . '/cache_data/';

        $mailer = (new EmailFallback\Builder)
            ->setServers($servers)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setNotificationFromEmail($_ENV['MREF_FROM_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->build();

        $this->assertInstanceOf(EmailFallback\Emailer::class, $mailer);
        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(0, $serverKey);
    }

    public function testConfigHelpers()
    {
        $servers = [
            0 => (new EmailFallback\Server)
                ->setHost('invalidHost') // deliberate invalid host
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

        $path = ($_ENV['MREF_FILES_PATH'] != '') ?
            $_ENV['MREF_FILES_PATH'] : __DIR__ . '/cache_data/';

        $driverConfig = [
            'path' => $path
        ];

        $mailer = (new EmailFallback\Builder)
            ->setServers($servers)
            ->setAdminEmails([$_ENV['MREF_ADMIN_EMAIL']])
            ->setNotificationFromEmail($_ENV['MREF_FROM_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->setRoundRobinMode(true)
            ->setRoundRobinDriver('files')
            ->setRoundRobinDriverConfig($driverConfig)
            ->setFromEmail('sender@example.com')
            ->setTos([$_ENV['MREF_ADMIN_EMAIL']])
            ->setCcs(['cc@example.com'])
            ->setBccs([$_ENV['MREF_ADMIN_EMAIL']])
            ->setReplyTos(['replyto@example.com', '2reply@example.com'])
            ->setSubject('Test Inquiry')
            ->setBody('Test Body')
            ->setDebugLevel(0)
            ->build();

        $this->assertInstanceOf(EmailFallback\Emailer::class, $mailer);

        $serverKey = $mailer->sendEmailFallback();

        $this->assertEquals(1, $serverKey);
    }
}
