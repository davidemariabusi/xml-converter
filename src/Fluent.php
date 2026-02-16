<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Dmb\XmlConverter\FluentException;

/**
 * Class Fluent
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
class Fluent implements FluentInterface
{
    /**
     * @var array<string, mixed>|array<int, mixed>
     */
    protected array $data;

    protected bool $isEmpty;

    protected bool $hasChildren;

    protected bool $hasAttributes;

    protected bool $hasValue;

    protected string $path;

    /**
     * @param  array<string, mixed>|array<int, mixed>  $data
     */
    protected function __construct(
        array $data = [],
        bool $isEmpty = false,
        string $path = '$'
    ) {
        $this->data = $data;
        $this->isEmpty = $isEmpty;
        $this->path = $path;
        $this->hasChildren = $this->detectHasChildren($data);
        $this->hasAttributes = $this->detectHasAttributes($data);
        $this->hasValue = array_key_exists('_value', $data);
    }

    /**
     * Factory method to create a new Fluent instance
     *
     * @param  array<string, mixed>  $data
     *
     * @throws FluentException
     */
    public static function make(
        array $data
    ): self {
        return new self($data, false, '$');
    }

    /**
     * Get the root element by name
     *
     * @param  string  $name  The root element key (e.g., 'SOAP-ENV:Envelope')
     */
    public function getRoot(
        string $name
    ): self {
        if ($this->isEmpty || ! array_key_exists($name, $this->data)) {
            $this->throwWithPath('Root element "'.$name.'" not found');
        }

        $rootData = $this->data[$name];

        return self::fromMixedNode($rootData, '$ > '.$name);
    }

    /**
     * Get a child element by tag name from _children
     *
     * Searches through _children array for an element with the given tag name.
     */
    public function getChild(
        string $name
    ): self {
        if ($this->isEmpty) {
            $this->throwWithPath('Cannot get child "'.$name.'" from an empty node');
        }

        $children = $this->extractChildren();

        foreach ($children as $child) {
            if (is_array($child) && array_key_exists($name, $child)) {
                $childData = $child[$name];

                return self::fromMixedNode($childData, $this->path.' > '.$name);
            }
        }

        $this->throwWithPath('Child element "'.$name.'" not found');
    }

    /**
     * Get all children as a new Fluent instance
     *
     * Returns Fluent containing the _children array, allowing iteration.
     */
    public function getChildren(): self
    {
        if ($this->isEmpty) {
            $this->throwWithPath('Cannot get children from an empty node');
        }

        $children = $this->extractChildren();

        if ($children === []) {
            $this->throwWithPath('No children found');
        }

        return new self($children, false, $this->path.' > _children');
    }

    /**
     * Get attributes as a new Fluent instance
     */
    public function getAttributes(): self
    {
        if ($this->isEmpty) {
            $this->throwWithPath('Cannot get attributes from an empty node');
        }

        if (! $this->hasAttributes) {
            $this->throwWithPath('No attributes found');
        }

        return new self($this->data['_attributes'], false, $this->path.' > _attributes');
    }

    /**
     * Get the raw value
     *
     * Returns _value if present, otherwise returns the full data array.
     */
    public function getValue(): mixed
    {
        if ($this->isEmpty) {
            $this->throwWithPath('No value found');
        }

        if ($this->hasValue) {
            return $this->data['_value'];
        }

        if ($this->data === []) {
            $this->throwWithPath('No value found');
        }

        return $this->data;
    }

    /**
     * Get the value as string or null
     */
    public function toString(): ?string
    {
        if ($this->isEmpty) {
            $this->throwWithPath('No value found to convert to string');
        }

        if ($this->hasValue) {
            return (string) $this->data['_value'];
        }

        return null;
    }

    /**
     * Check if this node is empty/null
     */
    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    /**
     * Check if this node is not empty
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty;
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return (array) $this->data;
    }

    /**
     * Get a specific attribute by name
     */
    public function getAttribute(
        string $name
    ): mixed {
        if ($this->isEmpty) {
            $this->throwWithPath('No attribute found with name "'.$name.'"');
        }

        if ($this->hasAttributes && array_key_exists($name, $this->data['_attributes'])) {
            return $this->data['_attributes'][$name];
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $this->throwWithPath('No attribute found with name "'.$name.'"');
    }

    /**
     * Check if node has children
     */
    public function hasChildren(): bool
    {
        return $this->hasChildren;
    }

    /**
     * Check if node has attributes
     */
    public function hasAttributes(): bool
    {
        return $this->hasAttributes;
    }

    /**
     * Check if node has a scalar value
     */
    public function hasValue(): bool
    {
        return $this->hasValue;
    }

    /**
     * Iterate over iterable items and execute callback
     *
     * @param  callable(self, int|string, string|null): void  $callback
     */
    public function each(
        callable $callback
    ): self {
        foreach ($this->iterableItemsFor('each') as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);
            $callback($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag']);
        }

        return $this;
    }

    /**
     * Transform iterable items and return a new Fluent list
     *
     * @param  callable(self, int|string, string|null): mixed  $callback
     */
    public function map(
        callable $callback
    ): self {
        $results = [];

        foreach ($this->iterableItemsFor('map') as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);
            $results[] = $callback($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag']);
        }

        return new self($results, false, $this->path.' > map');
    }

    /**
     * Filter iterable items and return a new Fluent list
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function filter(
        ?callable $callback = null
    ): self {
        $results = [];

        foreach ($this->iterableItemsFor('filter') as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);

            if ($callback !== null) {
                $shouldKeep = (bool) $callback($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag']);
            } else {
                $shouldKeep = $normalizedItem['item']->hasValue()
                    ? (bool) $normalizedItem['item']->getValue()
                    : (bool) $normalizedItem['item']->toArray();
            }

            if ($shouldKeep) {
                $results[] = $item;
            }
        }

        return new self($results, false, $this->path.' > filter');
    }

    /**
     * Get the first iterable item, optionally matching a callback
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function first(
        ?callable $callback = null
    ): self {
        $items = $this->iterableItemsFor('first');

        foreach ($items as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);

            if ($callback === null || (bool) $callback($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag'])) {
                return $normalizedItem['item'];
            }
        }

        $this->throwWithPath('First element not found');
    }

    /**
     * Get the last iterable item, optionally matching a callback
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function last(
        ?callable $callback = null
    ): self {
        $items = $this->iterableItemsFor('last');
        $match = null;

        foreach ($items as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);

            if ($callback === null || (bool) $callback($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag'])) {
                $match = $normalizedItem['item'];
            }
        }

        if ($match instanceof self) {
            return $match;
        }

        $this->throwWithPath('Last element not found');
    }

    /**
     * Get iterable item by zero-based index
     */
    public function at(
        int $index
    ): self {
        if ($index < 0) {
            $this->throwWithPath('Index must be greater than or equal to 0');
        }

        return $this->nth($index + 1);
    }

    /**
     * Get iterable item by one-based position
     */
    public function nth(
        int $n
    ): self {
        if ($n <= 0) {
            $this->throwWithPath('N must be greater than 0');
        }

        return $this->getOrdinalItem($n, $this->ordinalLabel($n));
    }

    /**
     * Get the second iterable item
     */
    public function second(): self
    {
        return $this->nth(2);
    }

    /**
     * Get the third iterable item
     */
    public function third(): self
    {
        return $this->nth(3);
    }

    /**
     * Get the fourth iterable item
     */
    public function fourth(): self
    {
        return $this->nth(4);
    }

    /**
     * Get the fifth iterable item
     */
    public function fifth(): self
    {
        return $this->nth(5);
    }

    /**
     * Get the sixth iterable item
     */
    public function sixth(): self
    {
        return $this->nth(6);
    }

    /**
     * Get the seventh iterable item
     */
    public function seventh(): self
    {
        return $this->nth(7);
    }

    /**
     * Get the eighth iterable item
     */
    public function eighth(): self
    {
        return $this->nth(8);
    }

    /**
     * Get the ninth iterable item
     */
    public function ninth(): self
    {
        return $this->nth(9);
    }

    /**
     * Get the tenth iterable item
     */
    public function tenth(): self
    {
        return $this->nth(10);
    }

    /**
     * Pluck a strict path from iterable items
     *
     * @return array<int, mixed>
     */
    public function pluck(
        string $path
    ): array {
        $segments = explode('.', $path);
        $values = [];

        foreach ($this->iterableItemsFor('pluck') as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);
            $values[] = $this->resolvePathValue($normalizedItem['item'], $segments, $path);
        }

        return $values;
    }

    /**
     * Check if iterable items contain a value or satisfy a callback
     */
    public function contains(
        mixed $needle,
        bool $strict = true
    ): bool {
        foreach ($this->iterableItemsFor('contains') as $key => $item) {
            $normalizedItem = $this->normalizeIterableItem($item, $key);

            if (is_callable($needle)) {
                if ((bool) $needle($normalizedItem['item'], $normalizedItem['key'], $normalizedItem['tag'])) {
                    return true;
                }

                continue;
            }

            $candidate = $normalizedItem['item']->hasValue()
                ? $normalizedItem['item']->getValue()
                : $normalizedItem['item']->toArray();

            if ($strict) {
                if ($candidate === $needle) {
                    return true;
                }

                continue;
            }

            if ($this->valuesMatchLoosely($candidate, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Count children
     */
    public function count(): int
    {
        if ($this->isEmpty) {
            return 0;
        }

        $children = $this->extractChildren();

        return is_array($children) ? count($children) : 0;
    }

    /**
     * Create an empty Fluent instance (Null Object)
     */
    protected static function fromMixedNode(
        mixed $node,
        string $path
    ): self {
        if (is_array($node)) {
            return new self($node, false, $path);
        }

        return new self(['_value' => $node], false, $path);
    }

    /**
     * @param  array<string, mixed>|array<int, mixed>  $data
     */
    protected function detectHasChildren(
        array $data
    ): bool {
        if (array_is_list($data)) {
            return $data !== [];
        }

        if (! array_key_exists('_children', $data) || ! is_array($data['_children'])) {
            return false;
        }

        return $data['_children'] !== [];
    }

    /**
     * @param  array<string, mixed>|array<int, mixed>  $data
     */
    protected function detectHasAttributes(
        array $data
    ): bool {
        if (! array_key_exists('_attributes', $data) || ! is_array($data['_attributes'])) {
            return false;
        }

        return $data['_attributes'] !== [];
    }

    /**
     * @return array<int, mixed>
     */
    protected function extractChildren(): array
    {
        if (array_is_list($this->data)) {
            return $this->data;
        }

        if (! array_key_exists('_children', $this->data) || ! is_array($this->data['_children'])) {
            return [];
        }

        return $this->data['_children'];
    }

    protected function throwWithPath(
        string $message
    ): never {
        throw new FluentException($message.' [path: '.$this->path.']', 1);
    }

    /**
     * @return array<int|string, mixed>
     *
     * @throws FluentException
     */
    protected function iterableItemsFor(
        string $operation
    ): array {
        if ($this->isEmpty) {
            $this->throwWithPath('Cannot '.$operation.' on an empty node');
        }

        if (array_is_list($this->data)) {
            return $this->data;
        }

        if (array_key_exists('_children', $this->data) && is_array($this->data['_children'])) {
            return $this->data['_children'];
        }

        $this->throwWithPath('Cannot '.$operation.' on a non-iterable node');
    }

    /**
     * @return array{item: self, key: int|string, tag: string|null}
     */
    protected function normalizeIterableItem(
        mixed $item,
        int|string $key
    ): array {
        $itemPath = $this->path.' > ['.$key.']';

        if (is_array($item) && ! array_is_list($item) && count($item) === 1) {
            $tag = array_key_first($item);

            if ($tag !== null) {
                return [
                    'item' => self::fromMixedNode($item[$tag], $this->path.' > '.$tag),
                    'key' => $key,
                    'tag' => (string) $tag,
                ];
            }
        }

        return [
            'item' => self::fromMixedNode($item, $itemPath),
            'key' => $key,
            'tag' => null,
        ];
    }

    protected function getOrdinalItem(
        int $ordinal,
        string $label
    ): self {
        $position = 1;

        foreach ($this->iterableItemsFor(mb_strtolower($label)) as $key => $item) {
            if ($position === $ordinal) {
                $normalizedItem = $this->normalizeIterableItem($item, $key);

                return $normalizedItem['item'];
            }

            $position++;
        }

        $this->throwWithPath($label.' element not found');
    }

    protected function ordinalLabel(
        int $ordinal
    ): string {
        return match ($ordinal) {
            1 => 'First',
            2 => 'Second',
            3 => 'Third',
            4 => 'Fourth',
            5 => 'Fifth',
            6 => 'Sixth',
            7 => 'Seventh',
            8 => 'Eighth',
            9 => 'Ninth',
            10 => 'Tenth',
            default => $ordinal.'th',
        };
    }

    protected function valuesMatchLoosely(
        mixed $left,
        mixed $right
    ): bool {
        if ($left === $right) {
            return true;
        }

        if ($left === null || $right === null) {
            return false;
        }

        if (is_bool($left) || is_bool($right)) {
            return (bool) $left === (bool) $right;
        }

        if (is_numeric($left) && is_numeric($right)) {
            return (string) $left === (string) $right
                || (float) $left === (float) $right;
        }

        if (is_scalar($left) && is_scalar($right)) {
            return (string) $left === (string) $right;
        }

        return false;
    }

    /**
     * @param  array<int, string>  $segments
     */
    protected function resolvePathValue(
        self $item,
        array $segments,
        string $path
    ): mixed {
        $current = $item->toArray();

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];

                continue;
            }

            if (
                is_array($current)
                && array_key_exists('_attributes', $current)
                && is_array($current['_attributes'])
                && array_key_exists($segment, $current['_attributes'])
            ) {
                $current = $current['_attributes'][$segment];

                continue;
            }

            $item->throwWithPath('Unable to pluck "'.$path.'"');
        }

        return $current;
    }
}
