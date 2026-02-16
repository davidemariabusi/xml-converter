
# XML Converter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dmb/xml-converter.svg?style=flat-square)](https://packagist.org/packages/dmb/xml-converter)

Convert XML to array, convert array to XML, and navigate parsed XML with a fluent API.

## Requirements

- PHP `^8.2`

## Installation

```bash
composer require dmb/xml-converter
```

## What this package does

- **`FromXml`**: converts an XML string into a normalized array structure.
- **`FromArray`**: converts a compatible array structure into XML.
- **`Fluent`**: provides safe, chainable navigation and collection-style helpers on parsed XML arrays.

## Array structure used by the package

The package uses a consistent structure:

- `'_attributes'` for XML attributes
- `'_value'` for text content
- `'_children'` for ordered/repeated child nodes

Example:

```php
[
    'ParentTag' => [
        '_attributes' => [
            'Version' => '1.0',
        ],
        '_children' => [
            [
                'Success' => [
                    '_attributes' => ['Version' => '1.4'],
                    '_children' => [
                        ['Foo' => []],
                    ],
                ],
            ],
        ],
    ],
]
```

## Usage

### 1) XML to array

```php
use Dmb\XmlConverter\FromXml;
use Dmb\XmlConverter\XmlParsingException;

$xml = <<<'XML'
<SOAP-ENV:Envelope Version="1.0">
  <SOAP-ENV:Body>
    <ParentTag Version="1.0" Target="Test">
      <Success Version="1.4"><Foo/></Success>
    </ParentTag>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

try {
    $array = FromXml::make()->convertToArray($xml);
} catch (XmlParsingException $e) {
    $error = $e->getMessage();
}
```

Notes:

- `convertToArray()` returns an array with the XML root element as top-level key.
- If XML is invalid, it throws `XmlParsingException`.

### 2) Array to XML

```php
use Dmb\XmlConverter\FromArray;

$payload = [
    'header' => [
        'version' => [
            '_attributes' => ['port' => '0000', 'host' => 'host'],
            '_value' => '1.0.0',
        ],
        'timestamp' => '20230116170354',
    ],
    'response' => [
        '_attributes' => ['type' => 'type', 'product' => 'item'],
        '_children' => [
            ['search' => ['_attributes' => ['number' => '123', 'time' => '0.00']]],
            ['nights' => ['_attributes' => ['number' => '11']]],
        ],
    ],
];

// Default root element (library default)
$xmlDefaultRoot = FromArray::make()->convertToXml($payload);

// Custom root element
$xmlCustomRoot = FromArray::make()->convertToXml($payload, 'envelope');

// Custom root element with attributes
$xmlCustomRootWithAttributes = FromArray::make()->convertToXml(
    $payload,
    [
        'rootElementName' => 'envelope',
        '_attributes' => [
            'xmlns' => 'https://github.com/davidemariabusi/xml-converter',
        ],
    ]
);
```

### 3) Fluent navigation API

```php
use Dmb\XmlConverter\Fluent;
use Dmb\XmlConverter\FluentException;

$data = [
    'SOAP-ENV:Envelope' => [
        '_attributes' => ['Version' => '1.0'],
        '_children' => [
            [
                'SOAP-ENV:Body' => [
                    '_children' => [
                        [
                            'ParentTag' => [
                                '_attributes' => ['Target' => 'Test'],
                                '_children' => [
                                    ['Success' => ['_attributes' => ['Version' => '1.4']]],
                                    ['Success' => ['_attributes' => ['Version' => '1.5']]],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

try {
    $parentTag = Fluent::make($data)
        ->getRoot('SOAP-ENV:Envelope')
        ->getChild('SOAP-ENV:Body')
        ->getChild('ParentTag');

    $target = $parentTag->getAttribute('Target'); // "Test"

    $versions = $parentTag
        ->getChildren()
        ->filter(fn (Fluent $item, int|string $key, ?string $tag): bool => $tag === 'Success')
        ->pluck('Version'); // ['1.4', '1.5']
} catch (FluentException $e) {
    $error = $e->getMessage();
}
```

Useful `Fluent` methods:

- Navigation: `getRoot()`, `getChild()`, `getChildren()`, `getAttributes()`
- Value/metadata: `getValue()`, `toString()`, `hasChildren()`, `hasAttributes()`, `hasValue()`
- Collections: `each()`, `map()`, `filter()`, `first()`, `last()`, `at()`, `nth()`, `second()` ... `tenth()`
- Query helpers: `pluck()`, `contains()`, `count()`, `toArray()`, `isEmpty()`, `isNotEmpty()`

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Credits

- [Davide Maria Busi](https://github.com/davidemariabusi)
- [spatie/array-to-xml](https://github.com/spatie/array-to-xml)

## License

This project is released under the MIT License. See [LICENSE.md](LICENSE.md).
