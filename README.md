# Livewire Wizard

[//]: # ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v/sjwatts119/livewire-wizard.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/sjwatts119/livewire-wizard&#41;)

[//]: # ([![GitHub Tests Action Status]&#40;https://img.shields.io/github/actions/workflow/status/sjwatts119/livewire-wizard/run-tests.yml?branch=main&label=tests&style=flat-square&#41;]&#40;https://github.com/sjwatts119/livewire-wizard/actions?query=workflow%3Arun-tests+branch%3Amain&#41;)

[//]: # ([![GitHub Code Style Action Status]&#40;https://img.shields.io/github/actions/workflow/status/sjwatts119/livewire-wizard/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square&#41;]&#40;https://github.com/sjwatts119/livewire-wizard/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain&#41;)

[//]: # ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/sjwatts119/livewire-wizard.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/sjwatts119/livewire-wizard&#41;)

A simple wizard component for Laravel Livewire. Supports an unlimited number of steps with optional custom rules for each.

## Installation

Install the package via composer:

```bash
composer require sjwatts119/livewire-wizard
```

## Creating A Wizard
To make your first wizard component, run the following command:

```bash
php artisan make:wizard
```

This command supports any arguments used in the [Livewire Make Command](https://livewire.laravel.com/docs/quickstart#create-a-livewire-component).

A new wizard class and a corresponding step view will have been created. The class will look like this:

```php
<?php

namespace App\Livewire;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;

class YourWizard extends Wizard
{
    public function wizardSteps(): array
    {
        return [
            WizardStep::make(
                title: 'Step 1',
                view: view('step-1'),
            ),
        ];
    }

    public function render(): View
    {
        return $this
            ->currentStep()
            ->authorise()
            ->view();
    }
}
```

## Creating Steps
To create a new Wizard Step, you can add a new `WizardStep` instance in the `wizardSteps()` array.

A `WizardStep` accepts:
- `title`: The name of the step. This must be unique from your other steps.
- `view`: The view to be rendered when the step is active.
- `canNavigate` *(Optional)*: A boolean closure, prevents access to a step unless the closure evaluates to `true`.

Here is an example of a contact form wizard with two steps:
```php
<?php

namespace App\Livewire;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;

class YourWizard extends Wizard
{
    public ?string $message = null;

    public function wizardSteps(): array
    {
        return [
            WizardStep::make(
                title: 'Your Message',
                view: view('livewire.wizard.message'),
            ),
            WizardStep::make(
                title: 'Your Details',
                view: view('livewire.wizard.details'),
                canNavigate: fn () => $this->message !== null,
            ),
        ];
    }

    // ...
}
```

## Rendering The Current Step
Internally, the wizard always keeps track of the current step. To retrieve an instance of the current step, you can call:
```php
$this->currentStep();
```

If you'd like to retrieve the relevant view for the current step, you can call:
```php
$this->currentStep()->view();
```

## Authorising The Current Step
If any of your steps have `canNavigate` closures, you can run these before rendering the view:
```php
$this->currentStep()
    ->authorise()
    ->view();
```

The `authorise()` method will abort with a 403 response if the closure returns false. 

Optionally, you can pass a false boolean value to the `aborts` parameter of `authorise()`. 
```php
$this->currentStep()
    ->authorise(aborts: false)
    ->view();
```
This will cause a `StepNotAuthorisedException` to be thrown when the rules return false instead of aborting the request.


## Credits

- [Sam Watts](https://github.com/sjwatts119)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
