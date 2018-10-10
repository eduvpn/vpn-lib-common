# Changelog

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
