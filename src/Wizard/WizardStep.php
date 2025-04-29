<?php

namespace SamWatts\LivewireWizard\Wizard;

use Closure;
use Illuminate\View\View;
use SamWatts\LivewireWizard\Exceptions\Wizard\StepNotAuthorisedException;

class WizardStep
{
    public function __construct(
        public string $title,
        public View $view,
        public ?Closure $canNavigate = null,
    ) {}

    /**
     * Create a new step instance.
     *
     * Optionally, you can pass a boolean closure which is evaluated when authorising the step.
     */
    public static function make(
        string $title,
        View $view,
        ?Closure $canNavigate = null,
    ): self {
        return new self($title, $view, $canNavigate);
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
     * Returns the step if the rules are met, otherwise aborts the request by default.
     *
     * Optionally, you can pass a boolean to choose whether to abort the request or throw an exception on failure.
     *
     * @throws StepNotAuthorisedException
     */
    public function authorise(bool $aborts = true): self
    {
        if (is_null($this->canNavigate)) {
            return $this;
        }

        if (($this->canNavigate)()) {
            return $this;
        }

        if ($aborts) {
            abort(
                code: 403,
                message: 'You are not authorised to view this step.',
            );
        }

        throw new StepNotAuthorisedException(
            previousStep: $this->getTitle(),
            targetStep: $this->getTitle(),
        );
    }

    /**
     * Run the authorisation rules for the step.
     * Returns true if no rules have been set or if the rules are met, otherwise false.
     */
    public function canNavigate(): bool
    {
        try {
            $this->authorise(aborts: false);

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
