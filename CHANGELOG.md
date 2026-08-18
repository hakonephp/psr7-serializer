# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-08-19

### Changed

- The minimum supported PHP version is now 8.2 ([#2](https://github.com/hakonephp/psr7-serializer/pull/2)).
- `Psr7Serializer`, `SerializableResponse`, and `SerializableStream` are now readonly classes ([#2](https://github.com/hakonephp/psr7-serializer/pull/2)).
- `unserializeResponse()` now uses explicit nullable parameter types ([#2](https://github.com/hakonephp/psr7-serializer/pull/2)).
- GitHub source archives now exclude development files and only ship `LICENSE`, `README.md`, `composer.json`, and `src/` ([#2](https://github.com/hakonephp/psr7-serializer/pull/2)).

[Unreleased]: https://github.com/hakonephp/psr7-serializer/compare/0.1.0...HEAD
[0.1.0]: https://github.com/hakonephp/psr7-serializer/compare/0.0.2...0.1.0
