<?php

namespace SamWatts\LivewireWizard\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\Component;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepDefinitionException;
use SamWatts\LivewireWizard\Wizard\WizardStep;

abstract class Wizard extends Component
{
    #[Session, Locked]
    public string $step;

    abstract public function wizardSteps(): array;

    /**
     * @throws StepDefinitionException
     */
    public function validateSteps(Collection $steps): void
    {
        if ($steps->isEmpty()) {
            throw new StepDefinitionException;
        }

        if (($duplicates = $steps->duplicates(fn (WizardStep $step) => $step->getTitle()))->isNotEmpty()) {
            throw new StepDefinitionException(
                message: 'Duplicate step titles found: ' . $duplicates->implode(', ')
            );
        }
    }

    /**
     * @throws StepDefinitionException
     */
    public function steps(): Collection
    {
        $steps = collect($this->wizardSteps())
            ->ensure(WizardStep::class);

        $this->validateSteps($steps);

        return $steps->mapWithKeys(fn (WizardStep $step) => [$step->getTitle() => $step]);
    }

    /**
     * Get the current step title.
     * If no step is set, or the step is not found in the collection, set the step to the first step's title.
     *
     * @throws StepDefinitionException
     */
    private function getOrInitialiseCurrentStepTitle(): string
    {
        return !$this->step || !$this->steps()->has($this->step)
            ? $this->step = $this->firstStep()->getTitle()
            : $this->step;
    }

    /**
     * Get a step by its title. If the step is not found, return null.
     *
     * @throws StepDefinitionException
     */
    public function step(string $title): ?WizardStep
    {
        return $this->steps()->get($title);
    }

    /**
     * Get the first step.
     *
     * @throws StepDefinitionException
     */
    public function firstStep(): WizardStep
    {
        return $this->steps()->first();
    }

    /**
     * Get the last step.
     *
     * @throws StepDefinitionException
     */
    public function lastStep(): WizardStep
    {
        return $this->steps()->last();
    }

    /**
     * Get the current step. If there is no current step, return the first step.
     *
     * @throws StepDefinitionException
     */
    public function currentStep(): WizardStep
    {
        return $this->step($this->getOrInitialiseCurrentStepTitle());
    }

    /**
     * Get the next step. If there is no next step, return null.
     *
     * @throws StepDefinitionException
     */
    public function nextStep(): ?WizardStep
    {
        return $this->steps()
            ->after(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }

    /**
     * Get the previous step. If there is no previous step, return null.
     *
     * @throws StepDefinitionException
     */
    public function previousStep(): ?WizardStep
    {
        return $this->steps()
            ->before(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }
}
