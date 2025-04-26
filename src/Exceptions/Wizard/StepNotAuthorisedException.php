<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

class StepNotAuthorisedException extends StepException
{
    protected function defaultMessage(string $previousStep, string $targetStep): string
    {
        return "Attempted to access step {$targetStep} from step {$previousStep}, but required rules were not met.";
    }
}
