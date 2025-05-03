<?php

namespace SamWatts\LivewireWizard\Tests\Support\Components;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepDefinitionException;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;

class WizardWithInvalidSteps extends Wizard
{
    public function wizardSteps(): array
    {
        return [
            WizardStep::make(
                title: 'Step 1',
                view: view('test::step-1'),
            ),
            collect(),
        ];
    }

    /**
     * @throws StepDefinitionException
     */
    public function render(): View
    {
        return $this
            ->currentStep()
            ->view();
    }
}
