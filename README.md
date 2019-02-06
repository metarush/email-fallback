# metarush/email-fallback

Send email via SMTP host, if it fails, fallback/failover to another SMTP host.

Note: This library uses `PHPMailer`.

## Install

Install via composer as `metarush/email-fallback`

## Usage

Define your SMTP servers. You can add as many as you want.

    <?php

    use MetaRush\EmailFallback;

    $servers = [
        0 => (new EmailFallback\Server)
            ->setHost('host1')
            ->setUser('user1')
            ->setPass('pass2')
            ->setPort('25')
            ->setEncr('TLS'),
        1 => (new EmailFallback\Server)
            ->setHost('host2')
            ->setUser('user2')
            ->setPass('pass2')
            ->setPort('25')
            ->setEncr('TLS')
    ];

The fallback servers will be used in the order they are defined.
You must start the array key with `0` then increment by `1`.

Initialize library:

    $mailer = (new EmailFallback\Builder($servers))
        ->setAdminEmail('admin@example.com') // optional: get fallback notification
        ->setAppName('myapp') // optional: identify app on fallback notification
        ->build();

Use `PHPMailer` members normally:

    $mailer->From = 'sender@example.com';
    $mailer->addAddress('receiver@example.com');
    $mailer->Subject = 'Test Subject';
    $mailer->Body = 'Test Body';


**Important:** You must use the following method to send email with fallback:

    $mailer->sendEmailFallback();

### SMTP Server selector

If you want to send using a different server, input the key (you previously defined) in the parameter:

    $mailer->sendEmailFallback(1); // send using server with key 1

This is useful if server `0` didn't fail but the email is slow to arrive. E.g., On a "forgot password" UI, users can get the email faster if you create a "try again" UI then use an alternative server to send the email again.

## Notes

The fallback can go back from the start. If you defined 3 servers (`0`, `1`, and `2`) and you selected server `2` to send the email, it can fallback to server `0` then `1`.

If all servers fail, an exception `EmailFallback\Exception` will be thrown by `sendEmailFallback()`.