# phppptx Community Edition

[phppptx](https://www.phppptx.com) is a PHP library designed to dynamically generate presentations in PowerPoint format (PresentationML).

**phppptx Community Edition** is a free, reduced version of the full [phppptx](https://www.phppptx.com) library. It includes a limited subset of the features available in the commercial editions.

The commercial editions provide a much broader feature set, including support for templates, HTML content, charts, headers, footers, watermarks, merging, conversion plugin, encryption, digital signatures, JSON API, and improved performance, together with technical support. Visit the [phppptx site](https://www.phppptx.com) for the full feature list and available licenses.

## Requirements

- PHP >= 5.6
- `ext-dom`
- `ext-xml`
- `ext-exif`
- `ext-gd`
- `ext-mbstring`
- `ext-zip`

## Installation

### Install with Composer

```sh
composer require phpdocx/phppptx_community
```

### Download and install

Download the project files and include the bundled autoloader in your PHP script:

```php
<?php
require_once __DIR__ . '/Classes/Phppptx/Create/CreatePptx.php';
```

This loads the library classes automatically so you can use the phppptx classes in your project.

## Examples

The examples folder contains self-contained samples for all the public methods.

## Changelog

See CHANGELOG.md for release notes.

## License

This project is distributed under the terms described in the LICENSE file.