<?php

namespace SamWatts\LivewireWizard\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Session;
use Livewire\Component;
use SamWatts\LivewireWizard\Exceptions\Wizard\NoStepsDefinedException;
use SamWatts\LivewireWizard\Wizard\WizardStep;

abstract class Wizard extends Component
{
    #[Session]
    public string $step;

    abstract public function steps(): array;

    /**
     * @throws NoStepsDefinedException
     */
    private function stepsCollection(): Collection
    {
        $collection = collect($this->steps())
            ->ensure(WizardStep::class)
            ->mapWithKeys(fn (WizardStep $step) => [$step->title => $step]);

        if ($collection->isEmpty()) {
            throw new NoStepsDefinedException;
        }

        return $collection;
    }

    /**
     * Get the current step title.
     * If no step is set, or the step is not found in the collection, set the step to the first step's title.
     *
     * @throws NoStepsDefinedException
     */
    private function getOrInitialiseCurrentStepTitle(): string
    {
        return !$this->step || !$this->stepsCollection()->has($this->step)
            ? $this->step = $this->firstStep()->title
            : $this->step;
    }

    /**
     * Get the first step.
     *
     * @throws NoStepsDefinedException
     */
    public function firstStep(): WizardStep
    {
        return $this->stepsCollection()->first();
    }

    /**
     * Get the last step. If there is no last step, return null.
     *
     * @throws NoStepsDefinedException
     */
    public function lastStep(): ?WizardStep
    {
        return $this->stepsCollection()->last();
    }

    /**
     * Get the current step. If there is no current step, return the first step.
     *
     * @throws NoStepsDefinedException
     */
    public function currentStep(): ?WizardStep
    {
        return $this->step($this->getOrInitialiseCurrentStepTitle());
    }

    /**
     * Get the next step. If there is no next step, return null.
     *
     * @throws NoStepsDefinedException
     */
    public function nextStep(): ?WizardStep
    {
        return $this->stepsCollection()
            ->after(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }

    /**
     * Get the previous step. If there is no previous step, return null.
     *
     * @throws NoStepsDefinedException
     */
    public function previousStep(): ?WizardStep
    {
        return $this->stepsCollection()
            ->before(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }

    /**
     * Get a step by its title. If the step is not found, return null.
     *
     * @throws NoStepsDefinedException
     */
    public function step(string $title): ?WizardStep
    {
        return $this->stepsCollection()->get($title);
    }
}
