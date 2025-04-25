<?php

namespace SamWatts\LivewireWizard\Commands;

use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;

class MakeWizardCommand extends MakeCommand
{
    protected $signature = 'make:wizard {name} {--force} {--inline} {--test} {--pest} {--stub=../vendor/sjwatts119/livewire-wizard/stubs}';
}
