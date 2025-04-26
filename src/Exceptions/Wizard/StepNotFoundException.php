<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

class StepNotFoundException extends StepException
{
    protected function defaultMessage(string $previousStep, string $targetStep): string
    {
        return "Attempted to access step {$targetStep} from step {$previousStep}, but step {$targetStep} doesn't exist.";
    }
}
