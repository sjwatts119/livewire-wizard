<?php

namespace SamWatts\LivewireWizard;

use SamWatts\LivewireWizard\Commands\MakeWizardCommand;
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
            ->hasCommand(MakeWizardCommand::class);
    }
}
