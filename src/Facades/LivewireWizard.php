<?php

namespace SamWatts\LivewireWizard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SamWatts\LivewireWizard\LivewireWizard
 */
class LivewireWizard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SamWatts\LivewireWizard\LivewireWizard::class;
    }
}
