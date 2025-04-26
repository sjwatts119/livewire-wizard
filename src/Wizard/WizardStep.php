<?php

namespace SamWatts\LivewireWizard\Wizard;

use Closure;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepNotAuthorisedException;

class WizardStep
{
    public function __construct(
        public string $title,
        public View $view,
        public Closure|Validator|null $rules = null,
    ) {}

    /**
     * Create a new step instance.
     */
    public static function make(
        string $title,
        View $view,
        ?Closure $rule = null,
    ): self {
        return new self($title, $view, $rule);
    }

    /**
     * Get the title of the step.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Run the authorisation rules for the step.
     * Returns the step if the rules are met, otherwise throws an exception.
     *
     * @throws StepNotAuthorisedException
     */
    public function authorise(): self
    {
        if (is_null($this->rules)) {
            return self;
        }

        return ($this->rules)()
            ? $this
            : throw new StepNotAuthorisedException(
                message: "Attempted to get view '{$this->view->getName()}', but required rules were not met for step with title '{$this->title}'",
            );
    }

    /**
     * Run the authorisation rules for the step.
     * Returns true if no rules have been set or if the rules are met, otherwise false.
     */
    public function canNavigate(): bool
    {
        try {
            $this->authorise();

            return true;
        } catch (StepNotAuthorisedException) {
            return false;
        }
    }

    /**
     * Compare the step to another step.
     */
    public function is(WizardStep $step): bool
    {
        return $step->getTitle() === $this->getTitle();
    }

    /**
     * Get the view for the step. This does not run the authorisation rules.
     */
    public function view(): View
    {
        return $this->view;
    }
}
