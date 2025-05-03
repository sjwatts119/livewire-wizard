<?php

use SamWatts\LivewireWizard\Exceptions\Wizard\StepNotAuthorisedException;
use SamWatts\LivewireWizard\Wizard\WizardStep;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('can make a wizard step instance using new', function () {
    $view = view('test::step-1');

    $step = new WizardStep(
        title: 'Step 1',
        view: $view,
    );

    expect($step)->toBeInstanceOf(WizardStep::class);
});

it('can make a wizard step instance using the static make method', function () {
    $view = view('test::step-1');

    $step = WizardStep::make(
        title: 'Step 1',
        view: $view,
    );

    expect($step)->toBeInstanceOf(WizardStep::class);
});

it('can get the step title', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
    );

    expect($step->title())->toBe('Step 1');
});

it('can get the step view', function () {
    $view = view('test::step-1');

    $step = WizardStep::make(
        title: 'Step 1',
        view: $view,
    );

    expect($step->view())->toBe($view);
});

it('can make a step with canNavigate closure', function () {
    $closure = fn () => true;

    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
        canNavigate: $closure,
    );

    expect($step->canNavigate)
        ->toBeInstanceOf(Closure::class)
        ->and($step->canNavigate)
        ->toBe($closure);
});

it('compares step instances with is()', function () {
    $step1 = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
    );

    $step2 = $step1;

    $step3 = WizardStep::make(
        title: 'Step 2',
        view: view('test::step-2'),
    );

    expect($step2->is($step1))->toBeTrue()
        ->and($step1->is($step2))->toBeTrue()
        ->and($step1->is($step3))->toBeFalse()
        ->and($step3->is($step1))->toBeFalse();
});

/*
 * Step auth when canNavigate is null
 */
it('returns step instance when running authorise() and canNavigate is null', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
    );

    expect($step->authorise())->toBeInstanceOf(WizardStep::class);
});

it('returns true when running canNavigate() and canNavigate is null', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
    );

    expect($step->canNavigate())->toBeTrue();
});

/*
 * Step auth when canNavigate is true
 */
it('returns step instance when running authorise() and canNavigate is true', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
    );

    expect($step->authorise())->toBeInstanceOf(WizardStep::class);
});

it('returns true when running canNavigate() and canNavigate is true', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
        canNavigate: fn () => true,
    );

    expect($step->canNavigate())->toBeTrue();
});

/*
 * Step auth when canNavigate is false
 */
it('aborts when running authorise() and canNavigate is true', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
        canNavigate: fn () => false,
    );

    $step->authorise(aborts: true);
})->throws(HttpException::class, 'You are not authorised to view this step.');

it('throws a StepNotAuthorisedException when running authorise() and canNavigate is true', function () {
    $step = WizardStep::make(
        title: 'Step 1',
        view: view('test::step-1'),
        canNavigate: fn () => false,
    );

    $step->authorise(aborts: false);
})->throws(StepNotAuthorisedException::class, 'Attempted to access step Step 1, but required rules were not met.');
