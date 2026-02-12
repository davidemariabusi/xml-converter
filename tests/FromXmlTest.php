<?php

declare(strict_types=1);

use Dmb\XmlConverter\FromXml;
use Dmb\XmlConverter\Tests\Assets\Xml\ValidArray;
use Dmb\XmlConverter\XmlParsingException;

it('converts xml to a valid array', function () {
    $converted = FromXml::make()
        ->convertToArray(getValidXmlForXml());

    expect($converted)->toBeArray();
});

it('throws a parsing exception if xml is not valid', function () {
    FromXml::make()->convertToArray(getInvalidXmlForXml());
})->throws(XmlParsingException::class, PARSING_XML_ERROR);

it('converts xml to the expected array', function () {
    $convertedXml = FromXml::make()
        ->convertToArray(getValidXmlForXml());

    expect($convertedXml)->toEqual((new ValidArray())->get());
});
