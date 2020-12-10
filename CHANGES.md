# Changelog

## 2.2.4 (...)
- remove `tlsProtection` configuration option, `tls-crypt` will always be used

## 2.2.3 (2020-11-27)
- refactor `ProfileConfig`
- allow calls to `/qr` when enrolling for 2FA when 2FA is enforced

## 2.2.2 (2020-10-20)
- rework `Config` class to offer strict typing
- remove `CliParser` as it was barely used by the other projects
- unify the HTTP client so all components use the same one

## 2.2.1 (2020-03-30)
- swap 404/405 (issue #19)
 
## 2.2.0 (2020-02-13)
- remove `Tpl` class
- update for new `fkooman/secookie` version (4.0.0)

## 2.1.0 (2020-01-20)
- do not escape slashes (`/`) in JSON any longer
- switch to our own `SessionInterface` and `CookieInterface` so we can drop 
  fkooman/secookie dependency here
- refactor and simplify `Request` class
- refactor and simplify `FormAuthentication` by merging "Hook" and "Module" in 
  one class

## 2.0.8 (2019-12-10)
- implement `Request::getScheme()` and fix `Request::getAuthority()`
- support PiB when showing byte counts

## 2.0.7 (2019-12-02)
- strip whitespace in front and behind user name as e.g. LDAP treats user name
  with / without whitespace identical

## 2.0.6 (2019-09-25)
- implement static permissions for PDO|LDAP|RADIUS authentication backends
- implement string trimming function for templates

## 2.0.5 (2019-08-29)
- update `Tpl` class from `fkooman/tpl`
- support multiple translation files / overrides
- RADIUS/LDAP are now optional extensions in `composer.json`

## 2.0.4 (2019-08-13)
- add dnsSuffix configuration option

## 2.0.3 (2019-07-20)
- use timezone as configured in PHP as the default for showing dates and times
- LDAP: disable following referrals by default

## 2.0.2 (2019-06-07)
- add `InputValidation::uiLang`
- better validate `GET` and `POST` values to make sure they are of type 
  `string`
- remove deprecated `filter_var` parameters for URL validation

## 2.0.1 (2019-04-26)
- better error reporting when (internal) API calls fail

## 2.0.0 (2019-04-01)
- remove YubiKey support
- check whether file exists before reading it
- implement the ability to require 2FA
- switch to `Tpl`, completely remove Twig
- remove `tls-auth` and `enableCompression` from profile config, no longer 
  supported
- rename "entitlement" to "permission"

## 1.3.2 (2018-12-05)
- remove PHP error suppression
- fix issue where user could not logout when being asked for 2FA

## 1.3.1 (2018-11-28)
- rework "logout" to make it work for `MellonAuthentication` as well

## 1.3.0 (2018-11-22)
- implement `UserInfo::authTime` to obtain the time the user authenticated
- implement `InputValidation::expiresAt` to replace 
  `InputValidation::certExpireDays`
- update password instead of trying to add user when user already exists with
  `PdoAuth` (Sjors Haanen)

## 1.2.3 (2018-11-09)
- add InputValidation::certExpireDays
- remove blockSmb from default `ProfileConfig`
- add `blockLan` to block traffic to the local LAN

## 1.2.2 (2018-10-10)
- introduce `Json` helper class
- remove XML from `eduPersonTargetedId` if used as SAML attribute for user 
  identification
- delete `_last_authenticated_at_ping_sent` session variable at logout
- implement "entitlementList" validator

## 1.2.1 (2018-09-10)
- introduce "entitlement" checker hook to check whether an authenticated user
  is allowed to use the admin portal
- simplify attribute fetch of authenticated user for LDAP
- remove userIdAuthorization from SAML plugin
- introduce `Request::optionalHeader`, `Request::requireHeader` for API
  simplication
- no longer have the concept of "admin" entitlement
- remove `Request::getHeader`

## 1.2.0 (2018-08-15)
- rewrite authorization layer, introduce "entitlements"
- switch SAML backend to use entitlements
- implement entitlements in LDAP backend

## 1.1.17 (2018-08-05)
- internal API calls now better typed
- lots of `vimeo/psalm` fixes

## 1.1.16 (2018-06-06)
- replace `tlsCrypt` option with `tlsProtection`

## 1.1.15 (2018-05-16)
- forgot to expose `_two_factor_user_id` for error pages

## 1.1.14 (2018-05-14)
- expose `_two_factor_user_id` to two factor verification template

## 1.1.13 (2018-04-16)
- replace `useNat` with `enableNat4` and `enableNat6` to allow separate 
  configuration for whether or not to enable NAT. For example: use NAT for IPv4
  and public IP addresses for IPv6

## 1.1.12 (2018-04-05)
- remove `authPlugin` configuration option, it will be autodetected now

## 1.1.11 (2018-03-29)
- support specifying multiple RADIUS servers

## 1.1.10 (2018-03-16)
- add RADIUS authentication plugin

## 1.1.9 (2018-03-15)
- delete cached user groups on logout
- switch to `UserInfo` from just userId string

## 1.1.8 (2018-02-27)
- introduce minimum password length input validation

## 1.1.7 (2018-02-26)
- introduce `exposedVpnProtoPorts`

## 1.1.6 (2018-02-23)
- implement `PdoAuth` method to check if user exists
- implement `InputValidation::voucherCode`

## 1.1.5 (2018-02-19)
- implement method to update password in `PdoAuth`

## 1.1.4 (2018-02-17)
- add `PdoAuth` class for storing users and password hashes in a database

## 1.1.3 (2017-12-14)
- support 160 bits TOTP secrets

## 1.1.2 (2017-12-12)
- `Service` class did not catch `InputValidationException` properly
- make `InputValidationException` extend `HttpException` now

## 1.1.1 (2017-11-27)
- make sure we use LDAPv3
- better LDAP error messages

## 1.1.0 (2017-11-23)
- implement `CredentialValidatorInterface` for verifying username/password
- switch `FormAuthentication` to use `CredentialValidatorInterface`
- implement `SimpleAuth` that verifies static username/password list used so
  far with `FormAuthentication`
- implement `LdapAuth`
- add `LdapClient` implementation

## 1.0.8 (2017-11-20)
- make compression (`--comp-lzo`) configurable

## 1.0.7 (2017-11-08)
- fix missing PATH_INFO and add test for it (issue #5)
- add `Response::import` to allow easy construction of `Response` object, 
  working around sub-optimal API
- cleanup `Response` a little bit without breaking API
- support PHPUnit 6

## 1.0.6 (2017-10-26)
- fix PHP >= 7.2 compatibility with `count()`
- fix risky tests

## 1.0.5 (2017-10-20)
- make `InputValidation::userId` validate the string is actually valid UTF-8 
  and check the length of the userId
- remove `InputValidation::languageCode` as it is not used anywhere
- add `authPlugin` configuration option to use external plugin to validate 
  2FA

## 1.0.4 (2017-10-02)
- make `InputValidation::userId` a NOP, all UTF-8 characters should be allowed 
  as `userId`
- no longer require `libsodium` as this library has no crypto needs

## 1.0.3 (2017-09-11)
- rename UI language cookie to no longer need to explicitly bind it to Path and 
  Domain without breaking language selector

## 1.0.2 (2017-09-10)
- update `fkooman/secookie`

## 1.0.1 (2017-08-17)
- small bugfix in `Config::hasSection()`

## 1.0.0 (2017-06-30)
- initial release
