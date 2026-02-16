<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

/**
 * Interface FluentInterface
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
interface FluentInterface
{
    /**
     * Factory method to create a new instance
     *
     * @param  array<string, mixed>  $data
     */
    public static function make(array $data): self;

    /**
     * Get the root element by name
     */
    public function getRoot(string $name): self;

    /**
     * Get a child element by tag name from _children
     */
    public function getChild(string $name): self;

    /**
     * Get all children as a new Fluent instance
     */
    public function getChildren(): self;

    /**
     * Get attributes as a new Fluent instance
     */
    public function getAttributes(): self;

    /**
     * Get the raw value (_value or the data itself)
     */
    public function getValue(): mixed;

    /**
     * Get the value as string or null
     */
    public function toString(): ?string;

    /**
     * Get a specific attribute by name
     */
    public function getAttribute(string $name): mixed;

    /**
     * Check if node has children
     */
    public function hasChildren(): bool;

    /**
     * Check if node has attributes
     */
    public function hasAttributes(): bool;

    /**
     * Check if node has a scalar value
     */
    public function hasValue(): bool;

    /**
     * Iterate over iterable items and execute callback
     *
     * @param  callable(self, int|string, string|null): void  $callback
     */
    public function each(callable $callback): self;

    /**
     * Transform iterable items and return a new Fluent list
     *
     * @param  callable(self, int|string, string|null): mixed  $callback
     */
    public function map(callable $callback): self;

    /**
     * Filter iterable items and return a new Fluent list
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function filter(?callable $callback = null): self;

    /**
     * Get the first iterable item, optionally matching a callback
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function first(?callable $callback = null): self;

    /**
     * Get the last iterable item, optionally matching a callback
     *
     * @param  (callable(self, int|string, string|null): bool)|null  $callback
     */
    public function last(?callable $callback = null): self;

    /**
     * Get iterable item by zero-based index
     */
    public function at(int $index): self;

    /**
     * Get iterable item by one-based position
     */
    public function nth(int $n): self;

    /**
     * Get the second iterable item
     */
    public function second(): self;

    /**
     * Get the third iterable item
     */
    public function third(): self;

    /**
     * Get the fourth iterable item
     */
    public function fourth(): self;

    /**
     * Get the fifth iterable item
     */
    public function fifth(): self;

    /**
     * Get the sixth iterable item
     */
    public function sixth(): self;

    /**
     * Get the seventh iterable item
     */
    public function seventh(): self;

    /**
     * Get the eighth iterable item
     */
    public function eighth(): self;

    /**
     * Get the ninth iterable item
     */
    public function ninth(): self;

    /**
     * Get the tenth iterable item
     */
    public function tenth(): self;

    /**
     * Pluck a strict path from iterable items
     *
     * @return array<int, mixed>
     */
    public function pluck(string $path): array;

    /**
     * Check if iterable items contain a value or satisfy a callback
     */
    public function contains(mixed $needle, bool $strict = true): bool;

    /**
     * Check if this node is empty/null
     */
    public function isEmpty(): bool;

    /**
     * Check if this node is not empty
     */
    public function isNotEmpty(): bool;

    /**
     * Count children
     */
    public function count(): int;

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
