# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.6] - 2019-12-20

### Fixed

- Fixed namespace for Wallet in Controller/AuthControoler.

## [1.0.5] - 2019-12-19

### Changed

- Changes due to the reference direction change between user and his wallets (Wallets => User):
  - Added 'user' property to the 'wallet' entity.

### Added

- Added verification if wallet already exists on Controller/AuthController/syncWallets before deciding whether to update properties of existing wallet or create a new one.

## [1.0.4] - 2019-12-19

### Fixed

- Fixed package version in composer.json.

## [1.0.3] - 2019-12-19

### Fixed

- Fixed typo in composer.json.

## [1.0.2] - 2019-12-19

### Added

- Added additional parameters to the wallet entity.
- Consideration of additional wallet parameters when synchronizing wallets (wallet addresses, coin names, symbols, etc.).

## [1.0.1] - 2019-11-09

### Changed

- Renamed vendor's name from 'Blockstar' to 'Zumo'.
- Renamed PHP package namespace from 'blockstar/zumokit-bundle' to 'zumo/zumokit-bundle'
- Renamed root configuration parameter from 'zumo_zumokit' to 'zumokit'.
- Security update: changed minimal required version of symfony/dependency-injection and symfony/framework-bundle.

## [1.0.0] - 2019-11-11

Initial public release.
