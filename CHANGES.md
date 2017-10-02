# Changelog

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
