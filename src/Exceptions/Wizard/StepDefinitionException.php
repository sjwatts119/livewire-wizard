<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

use Exception;

class StepDefinitionException extends Exception
{
    protected function message(): string
    {
        return 'No steps have been defined.';
    }
}
