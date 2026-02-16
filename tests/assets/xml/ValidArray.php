<?php

declare(strict_types=1);

namespace Dmb\XmlConverter\Tests\Assets\Xml;

class ValidArray
{
    public function get(): array
    {
        return [
            'SOAP-ENV:Envelope' => [
                '_children' => [
                    [
                        'SOAP-ENV:Body' => [
                            '_children' => [
                                [
                                    'ParentTag' => [
                                        '_children' => [
                                            [
                                                'Success' => [
                                                    '_children' => [
                                                        [
                                                            'Foo' => [],
                                                        ],
                                                    ],
                                                    '_attributes' => [
                                                        'Version' => '1.4',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        '_attributes' => [
                                            'Version' => '1.0',
                                            'Target' => 'Test',
                                            'TimeStamp' => '2026-02-11T14:34:49+00:00',
                                            'PrimaryLangID' => 'en'
                                        ],
                                    ],
                                ],
                                [
                                    'ParentTag' => [
                                        '_children' => [
                                            [
                                                'Success' => [
                                                    '_children' => [
                                                        [
                                                            'Bar' => [],
                                                        ],
                                                    ],
                                                    '_attributes' => [
                                                        'Version' => '1.5',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'Success' => [
                                                    '_children' => [
                                                        [
                                                            'Fii' => [],
                                                        ],
                                                    ],
                                                    '_attributes' => [
                                                        'Version' => '1.6',
                                                        'Target' => 'Test',
                                                        'TimeStamp' => '2026-02-11T14:34:49+00:00',
                                                        'PrimaryLangID' => 'en'
                                                    ],
                                                ],
                                            ],
                                        ],
                                        '_attributes' => [
                                            'Version' => '1.0',
                                            'Target' => 'Test',
                                            'TimeStamp' => '2026-02-11T14:34:49+00:00',
                                            'PrimaryLangID' => 'en'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '_attributes' => [
                    'Version' => '1.0',
                ],
            ],
        ];
    }
}
