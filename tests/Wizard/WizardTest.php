<?php

use Livewire\Livewire;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithAuthorise;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithDuplicateSteps;
use SamWatts\LivewireWizard\Tests\Support\Components\WizardWithInvalidSteps;
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

it('throws an exception if an item in wizardSteps() is not a WizardStep', function () {
    Livewire::test(WizardWithInvalidSteps::class);
})->throws(Exception::class);

it('renders the first step by default', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertSuccessful()
        ->assertViewIs('test::step-1');
});

test('navigateToPreviousStep() does nothing if there is no previous step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToPreviousStep')
        ->assertViewIs('test::step-1');
});

test('navigateToNextStep() navigates to the next step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2');
});

test('navigateToNextStep() does nothing if there is no next step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2')
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2');
});

test('navigateToStep() navigates to a specific step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToStep', 'Step 2')
        ->assertViewIs('test::step-2');
});

test('navigateToStep() does nothing if the step does not exist', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToStep', 'Step 3')
        ->assertViewIs('test::step-1');
});

test('nextStep() returns the next step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('nextStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 2');
});

test('nextStep() returns null if there is no next step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2')
        ->call('nextStep')
        ->assertReturned(null);
})->depends('navigateToNextStep() navigates to the next step');

test('previousStep() returns the previous step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2')
        ->call('previousStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 1');
})->depends('navigateToNextStep() navigates to the next step');

test('previousStep() returns null if there is no previous step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('previousStep')
        ->assertReturned(null);
});

test('step() returns a specific step if it exists', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('step', 'Step 2')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 2');
});

test('step returns null if the step does not exist', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('step', 'Step 3')
        ->assertReturned(null);
});

test('firstStep() returns the first step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('firstStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 1');
});

test('lastStep() returns the last step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('lastStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 2');
});

test('currentStep() returns the current step', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('currentStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 1')
        ->call('navigateToNextStep')
        ->assertViewIs('test::step-2')
        ->call('currentStep')
        ->assertReturned(fn (array $step) => $step['title'] === 'Step 2');
})->depends('navigateToNextStep() navigates to the next step');

test('steps() returns all steps', function () {
    Livewire::test(WizardWithoutAuthorise::class)
        ->call('steps')
        ->assertReturned(fn (array $steps) => count($steps) === 2);
});

test('validatePropertiesForStep() validates properties inside the canNavigate closure', function () {
    Livewire::test(WizardWithAuthorise::class)
        ->assertViewIs('test::step-1')
        ->call('navigateToStep', 'Step 2')
        ->assertViewIs('test::step-2')
        ->set('name', 'Sam')
        ->call('navigateToStep', 'Step 3')
        ->assertViewIs('test::step-3')
        ->set('name', '')
        ->assertForbidden();
});
