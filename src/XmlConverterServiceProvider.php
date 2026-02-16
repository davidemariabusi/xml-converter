<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Dmb\XmlConverter\FromArray;
use Dmb\XmlConverter\FromXml;
use Illuminate\Support\ServiceProvider;

/**
 * Class XmlConverterServiceProvider
 *
 * @package Dmb\XmlConverter
 * @author Davide Mariabusi <davidemaria.busi@gmail.com>
 * @license MIT
 * @link https://github.com/davidemariabusi/xml-converter
 */
class XmlConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(FromArray::class, function () {
            return new FromArray();
        });

        $this->app->bind(FromXml::class, function () {
            return new FromXml();
        });

        $this->app->bind(Fluent::class, function () {
            return Fluent::make([]);
        });

        $this->app->bind(FluentInterface::class, function () {
            return Fluent::make([]);
        });
    }
}
