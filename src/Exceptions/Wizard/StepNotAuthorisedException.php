<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

use Exception;
use SamWatts\LivewireWizard\Wizard\WizardStep;
use Throwable;

class StepNotAuthorisedException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, ?WizardStep $step = null)
    {
        if ($step) {
            $message = $this->message($step);
        }

        if (empty($message)) {
            $message = 'You are not authorised to view this step.';
        }

        parent::__construct($message, $code, $previous);
    }

    protected function message(WizardStep $step): string
    {
        return "Attempted to access step {$step->title()}, but required rules were not met.";
    }
}
