<?php

namespace SamWatts\LivewireWizard\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\LivewireWizardServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:v2krEe2N3gBn6/ThzQ5YX/Ia8Vh4Zd/Z3prlDLr2A1c=');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'SamWatts\\LivewireWizard\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        View::addNamespace('test', __DIR__ . '/Support/resources/views');

        $this
            ->registerLivewireComponents();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireWizardServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }

    private function registerLivewireComponents(): self
    {
        Livewire::component('wizard', Wizard::class);

        return $this;
    }
}
