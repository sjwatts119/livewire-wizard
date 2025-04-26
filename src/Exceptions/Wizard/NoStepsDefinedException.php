<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

class NoStepsDefinedException extends StepException
{
    protected function defaultMessage(string $previousStep, string $targetStep): string
    {
        return 'No steps have been defined.';
    }
}
