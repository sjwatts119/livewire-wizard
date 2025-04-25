<?php

namespace SamWatts\LivewireWizard;

use SamWatts\LivewireWizard\Commands\LivewireWizardCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireWizardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('livewire-wizard')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_livewire_wizard_table')
            ->hasCommand(LivewireWizardCommand::class);
    }
}
