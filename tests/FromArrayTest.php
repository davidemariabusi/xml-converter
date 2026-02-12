<?php

declare(strict_types=1);

use Dmb\XmlConverter\FromArray;
use Dmb\XmlConverter\Tests\Assets\Array\ValidArray;

it('converts array to xml', function () {
    $convertedArray = FromArray::make()
        ->convertToXml(
            (new ValidArray())->get(),
            'envelope'
        );

    expect(simplexml_load_string($convertedArray))
        ->toEqual(simplexml_load_string(getValidXmlForArray()));
});
