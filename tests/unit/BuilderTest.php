<?php

use MetaRush\EmailFallback;

require_once __DIR__ . '/Common.php';

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
                ->setHost($_ENV['MREF_SMTP_HOST_1'])
                ->setUser($_ENV['MREF_SMTP_USER_1'])
                ->setPass($_ENV['MREF_SMTP_PASS_1'])
                ->setPort($_ENV['MREF_SMTP_PORT_1'])
                ->setEncr($_ENV['MREF_SMTP_ENCR_1'])
        ];

        $mailer = (new EmailFallback\Builder($servers))
            ->setAdminEmail($_ENV['MREF_ADMIN_EMAIL'])
            ->setFromEmail($_ENV['MREF_FROM_EMAIL'])
            ->setAppName($_ENV['MREF_APP_NAME'])
            ->build();

        $this->assertInstanceOf(EmailFallback\Emailer::class, $mailer);

        $mailer->From = 'sender@example.com';
        $mailer->addAddress($_ENV['MREF_ADMIN_EMAIL']);
        $mailer->Subject = 'Test inquiry';
        $mailer->Body = 'Test Body';

        $mailer->sendEmailFallback();

        $this->assertTrue(true);
    }
}
