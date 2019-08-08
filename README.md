# metarush/email-fallback

Send email via SMTP host, if it fails, fallback/failover to another SMTP host.
Round-robin mode also available to distribute the load to all SMTP hosts.

Note: This library uses `PHPMailer`.

## Install

Install via composer as `metarush/email-fallback`

## Usage

Define your SMTP servers. You can add as many as you want.

```php
<?php

use MetaRush\EmailFallback;

$servers = [
    0 => (new EmailFallback\Server)
        ->setHost('host1')
        ->setUser('user1')
        ->setPass('pass2')
        ->setPort(25)
        ->setEncr('TLS'),
    1 => (new EmailFallback\Server)
        ->setHost('host2')
        ->setUser('user2')
        ->setPass('pass2')
        ->setPort(25)
        ->setEncr('TLS')
];
```

The fallback servers will be used in the order they are defined.
You must start the array key with `0` then increment by `1`.

Initialize library:

```php
$mailer = (new EmailFallback\Builder)
    ->setServers($servers) // array of Server objects
    ->setFromEmail(string) // "From" email
    ->setTos(array) // array of "To" email
    ->setSubject(string) // email subject
    ->setBody(string) // email body
    ->setFromName(string) // optional: "From Name"
    ->setCcs(array) // optional: array of "CC" emails
    ->setBccs(array) // optional: array of "BCC" emails
    ->setReplyTos(array) // optional: array of "Reply to" emails
    ->setAttachments(array) // optional: array of files already on the server
    ->setDebugLevel(int) // optional: PHPMailer debug level from 0 - 4. Default: 0
    ->setAdminEmails(array) // optional: array of emails that will be notified if fallback occurs
    ->setNotificationFromEmail(string) // if you set an admin email, you must a "from" email for notifications
    ->setAppName(string) // optional: app name used on notifications
    ->build();
```


**Important:** You must use the following method to send email with fallback:

```php
$mailer->sendEmailFallback();
```

---

### SMTP server selector

If you want to send using a different server, input the server key in the parameter:

```php
$mailer->sendEmailFallback(1); // send using server with key 1
```

This is useful if server `0` didn't fail but the email is slow to arrive. E.g., On a "forgot password" UI, users can get the email faster if you create a "try again" UI then use an alternative server to send the email again.

**Note:**

The fallback can go back from the start. If you defined 3 servers (`0`, `1`, and `2`) and you selected server `2` to send the email, it can fallback to server `0` then `1`. If you enter a key that does not exist in the config, `0` will be used.

If all servers fail, an exception `EmailFallback\Exception` will be thrown by `sendEmailFallback()`.

---

### Round-robin mode

Use round-robin mode to distribute the load to all SMTP hosts.

To enable round-robin mode, you must use a storage driver to track the last server used to send email.

#### Available drivers and their config:

**files**

```php
$driver = 'files';
$driverConfig = [
    'path' => '/var/www/example/emailFallbackCache/'
];
```

**memcached**

```php
$driver = 'memcached';
$driverConfig = [
    'host'         => '127.0.0.1',
    'port'         => 11211,
    'saslUser'     => '',
    'saslPassword' => ''
];
```

Note: Only single server/non-distriubuted memcached is supported at the moment.

**redis**

```php
$driver = 'redis';
$driverConfig = [
    'host'      => '127.0.0.1',
    'port'      => 6379,
    'password'  => '',
    'database'  => 0
];
```

Note: Use `memcached` or `redis` if available as `files` is not recommended for heavy usage.

After selecting a driver, set the following in the builder, before the `->build();` method:

```php
->setRoundRobinMode(true)
->setRoundRobinDriver($driver)
->setRoundRobinDriverConfig($driverConfig)
```