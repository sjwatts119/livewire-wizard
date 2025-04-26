<?php

namespace SamWatts\LivewireWizard\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use SamWatts\LivewireWizard\Wizard\WizardStep;

abstract class Wizard extends Component
{
    #[Url(keep: true)]
    public string $step;

    public function boot(): void
    {
        $this->fallbackToDefaultStep();
    }

    abstract public function steps(): array;

    public function stepsCollection(): Collection
    {
        return Collection::make($this->steps())
            ->mapWithKeys(fn (WizardStep $step) => [$step->title => $step]);
    }

    private function fallbackToDefaultStep(): void
    {
        $this->step ??= $this->stepsCollection()->first()->title;
    }

    public function step(string $title): ?WizardStep
    {
        return $this->stepsCollection()->get($title);
    }

    public function currentStep(): ?WizardStep
    {
        return $this->step($this->step);
    }

    public function nextStep(): ?WizardStep
    {
        return $this->stepsCollection()
            ->after(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }

    public function previousStep(): ?WizardStep
    {
        return $this->stepsCollection()
            ->before(fn (WizardStep $step) => $step->getTitle() === $this->step, true);
    }
}
