# Changelog

All notable changes to xml-converter will be documented in this file.

## [Unreleased]

### Documentation

- Updated [README.md](README.md) to reflect current package capabilities:
	- clarified package purpose and requirements;
	- added installation and usage sections for `FromXml`, `FromArray`, and `Fluent`;
	- documented the normalized array structure (`_children`, `_attributes`, `_value`);
	- replaced outdated examples and fixed typos.

### Fixed

- Laravel service bindings for `Fluent` and `FluentInterface` now use `Fluent::make([])` instead of `new Fluent()`, aligning with `Fluent` protected constructor.
