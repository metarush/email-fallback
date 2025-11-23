# CHANGELOG

## 6.0.0 - 2025-11-24

### Changed

- Upgrade dependency `phpfastcache` to ^9.0.
- Upgrade PHP minimum requirement to 8.0.

## 5.0.0 - 2025-11-22

### Changed

- Upgrade dependency `phpfastcache` to ^8.0.
- Upgrade PHP minimum requirement to 7.4.

### Fixed

- Fix setCharSet() not fluent in in Config.

## 4.2.0 - 2023-09-06

### Added

- Add config `setCharSet()`.
- Set default character set to UTF-8.

## 4.1.0 - 2023-05-25

### Added

- Add config method `setCustomHeaders()`.
- Add additional error info from PHPMailer when all SMTP servers failed.

## 4.0.1 - 2019-08-10

### Fixed

- Fix code coverage so it's 100%.

## 4.0.0 - 2019-08-09

### Changed

- Change `setPort()` and `getPort()` type to `int` in `Server` class.

## 3.0.0 - 2019-03-25

### Changed

- Replace config method `setAdminEmail()` with `setAdminEmails()`.

## 2.0.0 - 2019-03-22

### Added

- Add helpers methods for composing email, related `PHPMailer` methods are overridden.

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

### Changed

- Rename method `addServers()` to `setServers()`
- Remove server parameter in `Builder` class constructor

## 1.0.3 - 2019-03-12

### Fixed

- Allow null return value in `getRoundRobinDriver()` and null param in `setRoundRobinDriver()` config method.

## 1.0.2 - 2019-02-17

### Fixed

- In the `sendEmailFallback()` method, if `$serverKey` param is set but value is not defined in config, server key 0 will be used.

## 1.0.1 - 2019-02-15

### Fixed

- Allow null parameter in `setAppName()` config method.

## 1.0.0 - 2019-02-03

- Release first version.
