<?php

namespace SamWatts\LivewireWizard\Commands;

use Illuminate\Console\Command;

class LivewireWizardCommand extends Command
{
    public $signature = 'livewire-wizard';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
