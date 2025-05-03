<?php

namespace SamWatts\LivewireWizard\Tests\Support\Components;

use Illuminate\View\View;
use Livewire\Attributes\Validate;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepDefinitionException;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepNotAuthorisedException;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;

class WizardWithAuthorise extends Wizard
{
    #[Validate('required')]
    public string $name = '';

    public function wizardSteps(): array
    {
        return [
            WizardStep::make(
                title: 'Step 1',
                view: view('test::step-1'),
            ),
            WizardStep::make(
                title: 'Step 2',
                view: view('test::step-2'),
                canNavigate: fn () => true,
            ),
            WizardStep::make(
                title: 'Step 3',
                view: view('test::step-3'),
                canNavigate: fn () => $this->validatePropertiesForStep('name'),
            ),
        ];
    }

    /**
     * @throws StepDefinitionException
     * @throws StepNotAuthorisedException
     */
    public function render(): View
    {
        return $this
            ->currentStep()
            ->authorise()
            ->view();
    }
}
