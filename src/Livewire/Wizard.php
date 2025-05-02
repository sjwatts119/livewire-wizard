<?php

namespace SamWatts\LivewireWizard\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepDefinitionException;
use SamWatts\LivewireWizard\Wizard\WizardStep;

abstract class Wizard extends Component
{
    #[Url, Locked]
    public string $step;

    abstract public function wizardSteps(): array;

    /**
     * @throws StepDefinitionException
     */
    private function validateSteps(Collection $steps): void
    {
        if ($steps->isEmpty()) {
            throw new StepDefinitionException('No steps have been defined.');
        }

        $steps->ensure(WizardStep::class);

        if (($duplicates = $steps->duplicates(fn (WizardStep $step) => $step->getTitle()))->isNotEmpty()) {
            throw new StepDefinitionException(
                message: 'Duplicate step titles found: ' . $duplicates->implode(', ')
            );
        }
    }

    /**
     * Run the validation rules for provided properties. Can be used within the `canNavigate` closure.
     *
     * @see \Livewire\Component::validateOnly()
     *
     * @throws StepDefinitionException
     */
    public function validatePropertiesForStep(string|array|Collection $properties): bool
    {
        try {
            if (is_string($properties)) {
                $this->validateOnly($properties);
            }

            collect($properties)
                ->each(fn ($property) => $this->validateOnly($property));

            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    /**
     * Get a collection of steps defined in the wizard keyed by their titles.
     *
     * @throws StepDefinitionException
     */
    public function steps(): Collection
    {
        $steps = collect($this->wizardSteps());

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
        return !isset($this->step) || !$this->steps()->has($this->step)
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

    /**
     * Navigate to the next step.
     * If there is no next step, do nothing.
     *
     * @throws StepDefinitionException
     */
    public function navigateToNextStep(): void
    {
        if ($nextStep = $this->nextStep()) {
            $this->step = $nextStep->getTitle();
        }
    }

    /**
     * Navigate to the previous step.
     * If there is no previous step, do nothing.
     *
     * @throws StepDefinitionException
     */
    public function navigateToPreviousStep(): void
    {
        if ($previousStep = $this->previousStep()) {
            $this->step = $previousStep->getTitle();
        }
    }

    /**
     * Navigate to a specific step by its title.
     *
     * @throws StepDefinitionException
     */
    public function navigateToStep(string $step): void
    {
        if ($this->step($step)) {
            $this->step = $step;
        }
    }
}
