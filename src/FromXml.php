<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Dmb\XmlConverter\XmlParsingException;
use DOMDocument;
use DOMNode;

/**
 * Class FromXml
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
class FromXml
{
    /**
     * Create a new instance of FromXml
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Convert an XML string to an array
     *
     * @throws XmlParsingException
     */
    public function convertToArray(
        string $xmlToConvert
    ): array {
        $this->validateXmlFromString($xmlToConvert);

        $doc = new DOMDocument();
        $doc->loadXML($xmlToConvert);
        $root = $doc->documentElement;

        if (! $root instanceof DOMNode || ! $root->nodeName) {
            return [];
        }

        $result = $this->domNodeToArray($root);

        return [$root->nodeName => $result];
    }

    /**
     * Convert a DOMNode to an array
     *
     * @param  DOMNode|null  $node
     * @return array<string, mixed>|string
     */
    public function domNodeToArray(
        ?DOMNode $node
    ): array|string {
        if (empty($node)) {
            return [];
        }

        $output = [];
        switch ($node->nodeType) {
            case 4:
            case 3:
                $output = trim($node->textContent);
                break;
            case 1:
                $children = [];
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domNodeToArray($child);
                    if (isset($child->tagName)) {
                        $children[] = [$child->tagName => $v];
                    } elseif ($v || $v === '0') {
                        $output = (string) $v;
                    }
                }

                if (is_array($output)) {
                    if (count($children) > 0) {
                        $output['_children'] = $children;
                    }

                    if ($node->attributes && $node->attributes->length) {
                        $a = [];
                        for ($j = 0; $j < $node->attributes->length; $j++) {
                            $attrNode = $node->attributes->item($j);
                            $a[$attrNode->nodeName] = $attrNode->nodeValue;
                        }
                        $output['_attributes'] = $a;
                    }
                } elseif ($node->attributes && $node->attributes->length) {
                    $a = [];
                    for ($j = 0; $j < $node->attributes->length; $j++) {
                        $attrNode = $node->attributes->item($j);
                        $a[$attrNode->nodeName] = $attrNode->nodeValue;
                    }
                    $output = ['_value' => $output, '_attributes' => $a];
                }
                break;
        }

        return $output;
    }

    /**
     * Validate an XML string
     *
     * @throws XmlParsingException
     */
    protected function validateXmlFromString(
        string $xmlToConvert
    ): void {
        libxml_use_internal_errors(true);

        $parsed = simplexml_load_string($xmlToConvert);

        if ($parsed === false) {
            throw new XmlParsingException();
        }
    }
}
