# Changelog

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
