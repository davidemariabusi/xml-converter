<?php

declare(strict_types=1);

use Dmb\XmlConverter\FluentException;
use Dmb\XmlConverter\Fluent;

describe('Fluent', function () {
    it('creates instance from array', function () {
        $data = ['root' => ['_attributes' => ['id' => '1']]];
        $fluent = Fluent::make($data);

        expect($fluent)->toBeInstanceOf(Fluent::class)
            ->and($fluent->isEmpty())->toBeFalse()
            ->and($fluent->toArray())->toBe($data);
    });

    it('navigates to root element and reads attributes', function () {
        $data = [
            'SOAP-ENV:Envelope' => [
                '_attributes' => ['Version' => '1.0'],
                '_children' => [],
            ],
        ];

        $fluent = Fluent::make($data);
        $root = $fluent->getRoot('SOAP-ENV:Envelope');

        expect($root->isEmpty())->toBeFalse()
            ->and($root->getAttribute('Version'))->toBe('1.0');
    });

    it('throws when root does not exist with path', function () {
        $data = ['other' => []];
        $fluent = Fluent::make($data);

        expect(fn () => $fluent->getRoot('nonexistent'))
            ->toThrow(FluentException::class, 'Root element "nonexistent" not found [path: $]');
    });

    it('navigates to child element by tag name', function () {
        $data = [
            '_children' => [
                ['Success' => ['_attributes' => ['Version' => '1.4']]],
                ['Error' => ['_attributes' => ['Code' => '500']]],
            ],
        ];

        $fluent = Fluent::make($data);
        $success = $fluent->getChild('Success');

        expect($success->isEmpty())->toBeFalse()
            ->and($success->getAttribute('Version'))->toBe('1.4');
    });

    it('throws when child does not exist with full path', function () {
        $data = [
            'root' => [
                '_children' => [
                    ['Success' => []],
                ],
            ],
        ];

        $fluent = Fluent::make($data);

        expect(fn () => $fluent->getRoot('root')->getChild('NonExistent'))
            ->toThrow(FluentException::class, 'Child element "NonExistent" not found [path: $ > root]');
    });

    it('allows chained navigation', function () {
        $data = [
            'SOAP-ENV:Envelope' => [
                '_children' => [
                    [
                        'SOAP-ENV:Body' => [
                            '_children' => [
                                [
                                    'ParentTag' => [
                                        '_attributes' => ['Version' => '1.0', 'Target' => 'Test'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $fluent = Fluent::make($data);
        $result = $fluent
            ->getRoot('SOAP-ENV:Envelope')
            ->getChild('SOAP-ENV:Body')
            ->getChild('ParentTag');

        expect($result->isEmpty())->toBeFalse()
            ->and($result->getAttribute('Version'))->toBe('1.0')
            ->and($result->getAttribute('Target'))->toBe('Test');
    });

    it('gets attributes', function () {
        $data = [
            '_attributes' => ['Version' => '1.0', 'Target' => 'Test'],
        ];

        $fluent = Fluent::make($data);
        $attrs = $fluent->getAttributes();

        expect($attrs->isEmpty())->toBeFalse()
            ->and($attrs->toArray())->toBe(['Version' => '1.0', 'Target' => 'Test']);
    });

    it('gets single attribute', function () {
        $data = [
            '_attributes' => ['Version' => '1.0'],
        ];

        $fluent = Fluent::make($data);

        expect($fluent->getAttribute('Version'))->toBe('1.0');
    });

    it('throws when attribute does not exist with path', function () {
        $data = [
            '_attributes' => ['Version' => '1.0'],
        ];

        $fluent = Fluent::make($data);

        expect(fn () => $fluent->getAttribute('NonExistent'))
            ->toThrow(FluentException::class, 'No attribute found with name "NonExistent" [path: $]');
    });

    it('gets children and counts them', function () {
        $data = [
            '_children' => [
                ['One' => []],
                ['Two' => []],
            ],
        ];

        $fluent = Fluent::make($data);
        $children = $fluent->getChildren();

        expect($children->isEmpty())->toBeFalse()
            ->and($children->count())->toBe(2);
    });

    it('throws when children do not exist with path', function () {
        $data = [];
        $fluent = Fluent::make($data);

        expect(fn () => $fluent->getChildren())
            ->toThrow(FluentException::class, 'No children found [path: $]');
    });

    it('gets value and string when _value exists', function () {
        $data = [
            '_value' => 'Hello World',
            '_attributes' => ['id' => '1'],
        ];

        $fluent = Fluent::make($data);

        expect($fluent->getValue())->toBe('Hello World')
            ->and($fluent->toString())->toBe('Hello World');
    });

    it('gets array as value when _value does not exist', function () {
        $data = ['_attributes' => ['id' => '1']];
        $fluent = Fluent::make($data);

        expect($fluent->getValue())->toBe($data)
            ->and($fluent->toString())->toBeNull();
    });

    it('counts children from list payload', function () {
        $data = [
            '_children' => [
                ['one' => []],
                ['two' => []],
                ['three' => []],
            ],
        ];

        $fluent = Fluent::make($data);

        expect($fluent->count())->toBe(3);
    });

    it('handles isNotEmpty correctly', function () {
        $data = ['root' => []];
        $fluent = Fluent::make($data);

        expect($fluent->isNotEmpty())->toBeTrue();
    });

    it('tracks hasChildren hasAttributes and hasValue for object nodes', function () {
        $data = [
            '_attributes' => ['id' => '1'],
            '_children' => [
                ['node' => []],
            ],
            '_value' => 'text',
        ];

        $fluent = Fluent::make($data);

        expect($fluent->hasChildren())->toBeTrue()
            ->and($fluent->hasAttributes())->toBeTrue()
            ->and($fluent->hasValue())->toBeTrue();
    });

    it('tracks hasChildren for children collections', function () {
        $data = [
            '_children' => [
                ['One' => []],
            ],
        ];

        $childrenCollection = Fluent::make($data)->getChildren();

        expect($childrenCollection->hasChildren())->toBeTrue()
            ->and($childrenCollection->hasAttributes())->toBeFalse()
            ->and($childrenCollection->hasValue())->toBeFalse();
    });

    it('handles scalar children as value nodes', function () {
        $data = [
            '_children' => [
                ['Description' => 'Hello world'],
            ],
        ];

        $description = Fluent::make($data)->getChild('Description');

        expect($description->hasValue())->toBeTrue()
            ->and($description->toString())->toBe('Hello world');
    });

    it('iterates children with each and keeps chaining context', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['id' => '1']]],
                ['item' => ['_attributes' => ['id' => '2']]],
            ],
        ];

        $fluent = Fluent::make($data);
        $visited = [];

        $result = $fluent->each(function (Fluent $item, int|string $key, ?string $tag) use (&$visited): void {
            $visited[] = [
                'key' => $key,
                'tag' => $tag,
                'id' => $item->getAttribute('id'),
            ];
        });

        expect($result)->toBe($fluent)
            ->and($visited)->toBe([
                ['key' => 0, 'tag' => 'item', 'id' => '1'],
                ['key' => 1, 'tag' => 'item', 'id' => '2'],
            ]);
    });

    it('throws when each is called on non iterable node', function () {
        $fluent = Fluent::make(['_attributes' => ['id' => '1']]);

        expect(fn () => $fluent->each(function (): void {}))
            ->toThrow(FluentException::class, 'Cannot each on a non-iterable node [path: $]');
    });

    it('maps iterable items to a new bif fluent list', function () {
        $mapped = Fluent::make(['alpha', 'beta'])
            ->map(fn (Fluent $item): string => mb_strtoupper((string) $item->toString()));

        expect($mapped->count())->toBe(2)
            ->and($mapped->first()->toString())->toBe('ALPHA')
            ->and($mapped->last()->toString())->toBe('BETA');
    });

    it('filters iterable items with callback', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['active' => 'true', 'id' => '1']]],
                ['item' => ['_attributes' => ['active' => 'false', 'id' => '2']]],
                ['item' => ['_attributes' => ['active' => 'true', 'id' => '3']]],
            ],
        ];

        $filtered = Fluent::make($data)
            ->filter(fn (Fluent $item): bool => $item->getAttribute('active') === 'true');

        expect($filtered->count())->toBe(2)
            ->and($filtered->first()->getAttribute('id'))->toBe('1')
            ->and($filtered->last()->getAttribute('id'))->toBe('3');
    });

    it('filters iterable items without callback removing falsy values', function () {
        $filtered = Fluent::make(['', 'yes', 0, '0', 1, null])->filter();

        expect($filtered->count())->toBe(2)
            ->and($filtered->first()->toString())->toBe('yes');
    });

    it('gets first and last item using callback', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['id' => '1', 'type' => 'a']]],
                ['item' => ['_attributes' => ['id' => '2', 'type' => 'b']]],
                ['item' => ['_attributes' => ['id' => '3', 'type' => 'a']]],
            ],
        ];

        $fluent = Fluent::make($data);

        expect($fluent->first(fn (Fluent $item): bool => $item->getAttribute('type') === 'a')->getAttribute('id'))->toBe('1')
            ->and($fluent->last(fn (Fluent $item): bool => $item->getAttribute('type') === 'a')->getAttribute('id'))->toBe('3');
    });

    it('throws when first does not find a match', function () {
        $fluent = Fluent::make(['a', 'b']);

        expect(fn () => $fluent->first(fn (Fluent $item): bool => $item->toString() === 'z'))
            ->toThrow(FluentException::class, 'First element not found [path: $]');
    });

    it('supports ordinal helpers second and third', function () {
        $fluent = Fluent::make(['first', 'second', 'third']);

        expect($fluent->second()->toString())->toBe('second')
            ->and($fluent->third()->toString())->toBe('third');
    });

    it('supports at with zero based index', function () {
        $fluent = Fluent::make(['zero', 'one', 'two']);

        expect($fluent->at(0)->toString())->toBe('zero')
            ->and($fluent->at(2)->toString())->toBe('two');
    });

    it('supports nth with one based position', function () {
        $fluent = Fluent::make(['first', 'second', 'third']);

        expect($fluent->nth(1)->toString())->toBe('first')
            ->and($fluent->nth(3)->toString())->toBe('third')
            ->and($fluent->nth(2)->toString())->toBe($fluent->second()->toString());
    });

    it('throws when at index is negative', function () {
        $fluent = Fluent::make(['one']);

        expect(fn () => $fluent->at(-1))
            ->toThrow(FluentException::class, 'Index must be greater than or equal to 0 [path: $]');
    });

    it('throws when nth is not positive', function () {
        $fluent = Fluent::make(['one']);

        expect(fn () => $fluent->nth(0))
            ->toThrow(FluentException::class, 'N must be greater than 0 [path: $]');
    });

    it('throws when ordinal helper is out of range', function () {
        $fluent = Fluent::make(['one', 'two']);

        expect(fn () => $fluent->tenth())
            ->toThrow(FluentException::class, 'Tenth element not found [path: $]');
    });

    it('plucks strict values from iterable items', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['id' => '1']]],
                ['item' => ['_attributes' => ['id' => '2']]],
            ],
        ];

        $ids = Fluent::make($data)->pluck('id');

        expect($ids)->toBe(['1', '2']);
    });

    it('throws on strict pluck when path is missing', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['id' => '1']]],
            ],
        ];

        expect(fn () => Fluent::make($data)->pluck('code'))
            ->toThrow(FluentException::class, 'Unable to pluck "code" [path: $ > item]');
    });

    it('contains supports callback and strict value comparison', function () {
        $data = [
            '_children' => [
                ['item' => ['_attributes' => ['id' => '1', 'active' => 'true']]],
                ['item' => ['_attributes' => ['id' => '2', 'active' => 'false']]],
            ],
        ];

        $fluent = Fluent::make($data);

        expect($fluent->contains(fn (Fluent $item): bool => $item->getAttribute('active') === 'true'))->toBeTrue()
            ->and(Fluent::make(['1'])->contains(1))->toBeFalse()
            ->and(Fluent::make(['1'])->contains(1, false))->toBeTrue();
    });

    it('throws when contains is called on non iterable node', function () {
        $fluent = Fluent::make(['_attributes' => ['id' => '1']]);

        expect(fn () => $fluent->contains('1'))
            ->toThrow(FluentException::class, 'Cannot contains on a non-iterable node [path: $]');
    });
});
