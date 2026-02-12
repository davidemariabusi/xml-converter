<?php

declare(strict_types=1);

namespace Dmb\XmlConverter\Tests\Assets\Array;

class ValidArray
{
    public function get(): array
    {
        return [
            'header' => [
                'version' => [
                    '_attributes' => [
                        'port' => '0000',
                        'host' => 'host',
                    ],
                    '_value' => '1.0.0'
                ],
                'timestamp' => '20230116170354',
            ],
            'response' => [
                'search' => [
                    '_attributes' => [
                        'number' => '123',
                        'time' => '0.00',
                    ],
                ],
                'nights' => [
                    '_attributes' => [
                        'number' => '11',
                    ],
                ],
                'checkin' => [
                    '_attributes' => [
                        'date' => '2023-05-15',
                    ],
                ],
                'checkout' => [
                    '_attributes' => [
                        'date' => '2023-05-26',
                    ],
                ],
                'hotels' => [
                    'hotel' => [
                        'agreement' => [
                            0 => [
                                'deadline' => [
                                    '_attributes' => [
                                        'date' => '2023-05-11 12:00:00',
                                        'value' => '2023-05-11 12:00:00',
                                    ],
                                ],
                                'policies' => [
                                    'policy' => [
                                        '_attributes' => [
                                            'from' => '2023-05-11 12:00:00',
                                            'percentage' => '9.09',
                                        ],
                                    ],
                                ],
                                'deadline_remarks' => [
                                ],
                                'remarks' => [
                                    'remark' => [
                                        0 => [
                                            '_attributes' => [
                                                'text' => 'Exclusive discount',
                                            ],
                                        ],
                                        1 => [
                                            '_attributes' => [
                                                'text' => 'BED AND BREAKFAST',
                                            ],
                                        ],
                                    ],
                                ],
                                'room' => [
                                    'price' => [
                                        'roomprice' => [
                                            '_attributes' => [
                                                'nett' => '772.25',
                                                'gross' => '772.25',
                                            ],
                                        ],
                                        '_attributes' => [
                                            'from' => '2023-05-15',
                                            'to' => '2023-05-16',
                                        ],
                                    ],
                                    '_attributes' => [
                                        'type' => 'dbl',
                                        'required' => '1',
                                        'occupancy' => '2',
                                    ],
                                ],
                                '_attributes' => [
                                    'id' => 'agreement-id',
                                    'available' => 'true',
                                    'room_basis' => 'RB',
                                    'meal_basis' => 'C',
                                    'ctype' => '00',
                                    'c_type' => '00',
                                    'room_type' => 'Quadruple Classic',
                                    'is_dynamic' => 'false',
                                    'currency' => 'CHF',
                                    'deadline' => '2023-05-11',
                                    'total' => '8494.75',
                                    'total_gross' => '8494.75',
                                    'original_total' => '8494.75',
                                    'special' => 'false',
                                ],
                            ],
                            1 => [
                                'deadline' => [
                                    '_attributes' => [
                                        'date' => '2023-05-11 12:00:00',
                                        'value' => '2023-05-11 12:00:00',
                                    ],
                                ],
                                'policies' => [
                                    'policy' => [
                                        '_attributes' => [
                                            'from' => '2023-05-11 12:00:00',
                                            'percentage' => '9.09',
                                        ],
                                    ],
                                ],
                                'deadline_remarks' => [
                                ],
                                'remarks' => [
                                    'remark' => [
                                        0 => [
                                            '_attributes' => [
                                                'text' => 'Exclusive discount',
                                            ],
                                        ],
                                        1 => [
                                            '_attributes' => [
                                                'text' => 'BED AND BREAKFAST',
                                            ],
                                        ],
                                    ],
                                ],
                                'room' => [
                                    'price' => [
                                        'roomprice' => [
                                            '_attributes' => [
                                                'nett' => '772.25',
                                                'gross' => '772.25',
                                            ],
                                        ],
                                        '_attributes' => [
                                            'from' => '2023-05-15',
                                            'to' => '2023-05-16',
                                        ],
                                    ],
                                    '_attributes' => [
                                        'type' => 'dbl',
                                        'required' => '1',
                                        'occupancy' => '2',
                                    ],
                                ],
                                '_attributes' => [
                                    'id' => 'agreement-id',
                                    'available' => 'true',
                                    'room_basis' => 'RB',
                                    'meal_basis' => 'C',
                                    'ctype' => '00',
                                    'c_type' => '00',
                                    'room_type' => 'Double Classic',
                                    'is_dynamic' => 'false',
                                    'currency' => 'CHF',
                                    'deadline' => '2023-05-11',
                                    'total' => '8494.75',
                                    'total_gross' => '8494.75',
                                    'original_total' => '8494.75',
                                    'special' => 'false',
                                ],
                            ],
                        ],
                        '_attributes' => [
                            'code' => '123456',
                            'name' => 'NAME',
                            'stars' => '2',
                            'location' => '00',
                            'address' => 'Address',
                            'promo' => 'false',
                            'city' => 'city',
                        ],
                    ],
                    '_attributes' => [
                        'total' => '1',
                    ],
                ],
                '_attributes' => [
                    'type' => 'type',
                    'product' => 'item',
                ],
            ]
            // '_root' => 'envelope',
        ];
    }
}
