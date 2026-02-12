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
                                    'ota:OTA_TourActivitySearchRS' => [
                                        '_children' => [
                                            [
                                                'ota:Success' => [
                                                    '_children' => [
                                                        [
                                                            'ota:Foo' => [],
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
                                            'PrimaryLangID' => 'en',
                                            'CorrelationID' => '698c938849ec86.73684605',
                                        ],
                                    ],
                                ],
                                [
                                    'ota:OTA_TourActivitySearchRS' => [
                                        '_children' => [
                                            [
                                                'ota:Success' => [
                                                    '_children' => [
                                                        [
                                                            'ota:Bar' => [],
                                                        ],
                                                    ],
                                                    '_attributes' => [
                                                        'Version' => '1.5',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'ota:Success' => [
                                                    '_children' => [
                                                        [
                                                            'ota:Fii' => [],
                                                        ],
                                                    ],
                                                    '_attributes' => [
                                                        'Version' => '1.6',
                                                        'Target' => 'Test',
                                                        'TimeStamp' => '2026-02-11T14:34:49+00:00',
                                                        'PrimaryLangID' => 'en',
                                                        'CorrelationID' => '698c938849ec86.73684605',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        '_attributes' => [
                                            'Version' => '1.0',
                                            'Target' => 'Test',
                                            'TimeStamp' => '2026-02-11T14:34:49+00:00',
                                            'PrimaryLangID' => 'en',
                                            'CorrelationID' => '698c938849ec86.73684605',
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
