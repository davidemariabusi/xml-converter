<?php

declare(strict_types=1);

namespace Dmb\XmlConverter;

use Dmb\XmlConverter\FromArray;
use Dmb\XmlConverter\FromXml;
use Illuminate\Support\ServiceProvider;

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
    }
}
