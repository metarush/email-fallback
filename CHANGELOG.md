# CHANGELOG

## 3.0.0

- Replace config method `setAdminEmail()` with `setAdminEmails()`

## 2.0.0

- Add helpers methods for composing email, related PHPMailer methods are
overridden.
    - `setFromEmail()`
    - `setTos()`
    - `setSubject()`
    - `setBody()`
    - `setFromName()`
    - `setCcs()`
    - `setBccs()`
    - `setReplyTos()`
    - `setAttachments()`
    - `setDebugLevel()`
- Rename method `addServers()` to `setServers()`
- Remove server parameter in Builder class constructor

## 1.0.3

- Allow null return value in `getRoundRobinDriver()` and null param in
`setRoundRobinDriver()` config method

## 1.0.2

- In the `sendEmailFallback()` method, if `$serverKey` param is set but value is
not defined in config, server key 0 will be used

## 1.0.1

- Allow null parameter in `setAppName()` config method

## 1.0.0

- Release first version.