<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature;

use AshAllenDesign\EmailUtilities\EmailUtilitiesProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider.
     *
     * @param  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [EmailUtilitiesProvider::class];
    }
}
