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

    public static function make(
        string $title,
        View $view,
        Closure|Validator|null $rules = null,
    ): self {
        return new self($title, $view, $rules);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @throws StepNotAuthorisedException
     */
    public function authorise(): self
    {
        if (is_null($this->rules)) {
            return self;
        }

        //        if ($this->rules instanceof Validator) {
        //            return $this->rules->passes()
        //                ? $this
        //                : throw new StepNotAuthorisedException(
        //                    message: "Attempted to get view '{$this->view->getName()}', but required rules were not met for step with title '{$this->title}'",
        //                );
        //        }

        return ($this->rules)()
            ? $this
            : throw new StepNotAuthorisedException(
                message: "Attempted to get view '{$this->view->getName()}', but required rules were not met for step with title '{$this->title}'",
            );
    }

    public function canNavigate(): bool
    {
        try {
            $this->authorise();

            return true;
        } catch (StepNotAuthorisedException) {
            return false;
        }
    }

    public function view(): View
    {
        return $this->view;
    }
}
