<?php

use Livewire\Livewire;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithDuplicateSteps;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithoutAuthorise;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithoutSteps;

it('can render a wizard component', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertSuccessful();
});

it('throws an exception if no steps are defined', function () {
    Livewire::test(WizardWithoutSteps::class);
})->throws(Exception::class, 'No steps have been defined.');

it('throws an exception if a duplicate step is defined', function () {
    Livewire::test(WizardWithDuplicateSteps::class);
})->throws(Exception::class, 'Duplicate step titles found: Step 1');
