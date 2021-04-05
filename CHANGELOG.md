# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2021-04-05
### Added
- PHP constraint for 7.3 and above
### Changed
- Detailed the composer api constraint
### Fixed
- PackageLoader::getGlobPatterns now returns empty array if vendor-path config is not set

## [0.1.1] - 2021-04-03

### Added
- Copyright headers to source code
### Changed
- dropped "appcode" from package name

## [0.1.0] - 2021-04-03
### Added
- Plugin is invoked on general "install" command
- Plugin detects vendor packages
- Plugin detects other app/code Modules
- Plugin detects platform dependencies
