<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function nextBookableDate(int $minDaysAhead = 1): string
    {
        $date = now()->addDays($minDaysAhead);
        while ($date->isSunday()) {
            $date->addDay();
        }

        return $date->toDateString();
    }

    protected function nextSunday(): string
    {
        $date = now()->addDay();
        while (! $date->isSunday()) {
            $date->addDay();
        }

        return $date->toDateString();
    }
}
