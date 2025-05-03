<?php

namespace SamWatts\LivewireWizard\Tests\Support\Components;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepDefinitionException;
use SamWatts\LivewireWizard\Livewire\Wizard;

class WizardWithoutSteps extends Wizard
{
    public function wizardSteps(): array
    {
        return [];
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
