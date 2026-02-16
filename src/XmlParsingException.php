<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Exception;
use Throwable;

/**
 * Class XmlParsingException
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
class XmlParsingException extends Exception
{
    public function __construct(
        string $message = "Parsing error: it seems that the xml file is invalid, please check and try again",
        int $code = 500,
        null|Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
