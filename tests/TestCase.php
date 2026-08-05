<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The test HTTP client inherits the real Accept-Language header from
        // the machine running the suite (e.g. "en-US,en;q=0.9"), which would
        // otherwise silently steer locale-dependent assertions off of the
        // app's mk default. Neutralize it here; individual tests that care
        // about Accept-Language detection override it explicitly.
        $this->withHeaders(['Accept-Language' => '']);
    }
}
