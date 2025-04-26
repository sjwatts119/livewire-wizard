<?php

namespace SamWatts\LivewireWizard\Exceptions\Wizard;

use Exception;

abstract class StepException extends Exception
{
    public function __construct(
        ?int $previousStep = null,
        ?int $targetStep = null,
        ?string $message = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $generatedMessage = $message ?? $this->defaultMessage($previousStep, $targetStep);
        parent::__construct($generatedMessage, $code, $previous);
    }

    abstract protected function defaultMessage(int $previousStep, int $targetStep): string;
}
