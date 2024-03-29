<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use DOMNode;

class FromXml
{
    /**
     * @param string $xmlToConvert
     * @throws XmlParsingExcpetion
     * @return array
     */
    public function convertToArray(
        string $xmlToConvert
    ): array {
        $this->validateXmlFromString($xmlToConvert);

        $doc = new \DOMDocument();
        $doc->loadXML($xmlToConvert);
        $root = $doc->documentElement;
        $output = (array) $this->domNodeToArray($root);

        return $output ?
            $output
            : [];
    }

    /**
     * @param string $xmlToConvert
     * @throws XmlParsingExcpetion
     * @return void
     */
    private function validateXmlFromString(
        string $xmlToConvert
    ): void {
        libxml_use_internal_errors(true);

        $parsed = simplexml_load_string($xmlToConvert);

        if ($parsed === false) {
            throw new XmlParsingExcpetion();
        }
    }

    /**
     * @param DOMNode|null $node
     * @return array|string
     */
    public function domNodeToArray($node)
    {
        if (empty($node)) {
            return [];
        }

        $output = [];
        switch ($node->nodeType) {
            case 4: // XML_CDATA_SECTION_NODE
            case 3: // XML_TEXT_NODE
                $output = trim($node->textContent);
                break;
            case 1: // XML_ELEMENT_NODE
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domNodeToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string) $v;
                    }
                }
                if ($node->attributes && $node->attributes->length && !is_array($output)) { // has attributes but isn't an array
                    $output = ['_value' => $output]; // change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes && $node->attributes->length) {
                        $a = [];
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['_attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if ($t !== '_attributes' && is_array($v) && count($v) === 1) {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }
}
