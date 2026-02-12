<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class FromArray
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
class FromArray
{
    /**
     * Create a new instance of FromArray
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Convert an array to XML
     *
     * @param array $arrayToConvert
     * @param string|array|null $rootElementName
     * @return string
     */
    public function convertToXml(
        array $arrayToConvert,
        string|array|null $rootElementName = null
    ): string {
        $transformed = $this->transformChildrenStructure($arrayToConvert);

        return ArrayToXml::convert(
            $transformed,
            $rootElementName,
            false
        );
    }

    /**
     * Transform the _children structure to ArrayToXml compatible format
     *
     * @param array $data
     * @return array
     */
    protected function transformChildrenStructure(
        array $data
    ): array {
        $result = [];

        foreach ($data as $key => $value) {
            if ($key === '_children' && is_array($value)) {
                $grouped = [];
                foreach ($value as $child) {
                    if (is_array($child)) {
                        foreach ($child as $tagName => $tagValue) {
                            $transformedValue = is_array($tagValue)
                                ? $this->transformChildrenStructure($tagValue)
                                : $tagValue;

                            if (!isset($grouped[$tagName])) {
                                $grouped[$tagName] = $transformedValue;
                            } else {
                                // Convert to array of elements if there are multiple
                                if (!isset($grouped[$tagName][0])) {
                                    $grouped[$tagName] = [$grouped[$tagName]];
                                }
                                $grouped[$tagName][] = $transformedValue;
                            }
                        }
                    }
                }
                $result = array_merge($result, $grouped);
            } elseif ($key === '_attributes') {
                $result['_attributes'] = $value;
            } elseif (is_array($value)) {
                $result[$key] = $this->transformChildrenStructure($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
