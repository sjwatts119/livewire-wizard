# A Simple Headless Livewire Wizard Component.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sjwatts119/livewire-wizard.svg?style=flat-square)](https://packagist.org/packages/sjwatts119/livewire-wizard)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sjwatts119/livewire-wizard/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sjwatts119/livewire-wizard/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sjwatts119/livewire-wizard/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sjwatts119/livewire-wizard/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sjwatts119/livewire-wizard.svg?style=flat-square)](https://packagist.org/packages/sjwatts119/livewire-wizard)

## [View Package Demo](https://livewire-wizard.sjwatts.com)

## Installation

Install the package via composer:

```bash
composer require sjwatts119/livewire-wizard
```

## Creating Your First Wizard
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
            ->view();
    }
}
```

## Creating Wizard Steps
To create a new Wizard Step, you can add a new `WizardStep` instance in the `wizardSteps()` array.

A `WizardStep` accepts:
- `title`: The name of the step. This must be unique from your other steps.
- `view`: The view to be rendered when the step is active.
- `canNavigate` *(Optional)*: A boolean closure, prevents access to a step unless the closure evaluates to `true`. For more information, see [Defining Step Rules](#defining-step-rules).

Here is an example of a contact form wizard with two steps:
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
                title: 'Your Message',
                view: view('livewire.wizard.message'),
            ),
            WizardStep::make(
                title: 'Your Details',
                view: view('livewire.wizard.details'),
            ),
        ];
    }

    public function render(): View
    {
        return $this
            ->currentStep()
            ->view();
    }
}
```

## Defining Step Rules
As mentioned previously, a WizardStep can accept a `canNavigate` closure. This closure will be called when the step is rendered, and should return a boolean value.

The wizard class exposes a helpful method `validatePropertiesForStep()` which can be used to validate any number of class properties. Internally, this uses [Livewire's Validation](https://livewire.laravel.com/docs/validation) to validate the properties, and returns a boolean value indicating whether the validation passed for all provided properties.

This validation also works with [Livewire Form Object](https://livewire.laravel.com/docs/forms#extracting-a-form-object) Properties. These should be referenced using dot notation, e.g. `form.name`.

For example, the following step will only be accessible if the validation rules defined on the name and message properties pass:
```php
<?php

namespace App\Livewire;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;

class YourWizard extends Wizard
{
    #[Validate('required')]
    public string $name = '';
    
    #[Validate('required')]
    public string $message = '';

    public function wizardSteps(): array
    {
        return [
            // Previous steps...

            WizardStep::make(
                title: 'Confirm',
                view: view('livewire.wizard.confirm'),
                canNavigate: fn () => $this->validatePropertiesForStep(['name', 'message']),
            ),
        ];
    }

    public function render(): View
    {
        return $this
            ->currentStep()
            ->authorise() // Validate the 'name' and 'message' properties if we are on step 'Confirm'.
            ->view();
    }
}
```

## Rendering The Wizard
If you'd like to retrieve the view for the current step, you can call:
```php
$this->currentStep()->view();
```

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

This will cause a `StepNotAuthorisedException` to be thrown when the `canNavigate` closure returns false instead of aborting the request. You could then gracefully handle this exception using [Livewire's Exception Lifecycle Hook](https://livewire.laravel.com/docs/lifecycle-hooks#exception). For example:
```php
namespace App\Livewire;

use Illuminate\View\View;
use SamWatts\LivewireWizard\Livewire\Wizard;
use SamWatts\LivewireWizard\Wizard\WizardStep;
use SamWatts\LivewireWizard\Exceptions\StepNotAuthorisedException;

class YourWizard extends Wizard
{
    /*
     * Gracefully handle the exception.
     */
    public function exception($e, $stopPropagation) {
        if ($e instanceof StepNotAuthorisedException) {
            $this->notify('Post is not found');
            $stopPropagation();
        }
    }

    public function wizardSteps(): array
    {
        return [
            // Steps with canNavigate closures...
        ];
    }

    public function render(): View
    {
        return $this
            ->currentStep()
            ->authorise(aborts: false) // Throws StepNotAuthorisedException if rules fail...
            ->view();
    }
}
```

## Navigation
In your step views, you likely want to add some navigation buttons to allow the user to move between steps.

You could use the [wire:click](https://livewire.laravel.com/docs/wire-click) directive to call any of the navigation methods on the wizard class. For example:
```html
<!-- Navigating to a specific step -->
<x-wizard.nav.item
    :current="$currentStep->is($step)"
    :disabled="!$step->canNavigate()"
    wire:click="navigateToStep('{{ $step->title() }}')"
>
    {{ $step->title() }}
</x-wizard.nav.item>

<!-- Navigating to the next step -->
<x-wizard.nav.item
    :disabled="!$nextStep->canNavigate()"
    wire:click="navigateToNextStep()"
>
    Next
</x-wizard.nav.item>

<!-- Navigating to the previous step -->
<x-wizard.nav.item
    :disabled="!$previousStep->canNavigate()"
    wire:click="navigateToPreviousStep()"
>
    Back
</x-wizard.nav.item>
```

## Wizard Methods
The wizard component has a number of public methods which can be used to get information about the current state of the wizard. These methods are:

```php
$this->steps(); // Returns a Collection of all WizardStep instances, keyed by their titles.
```
```php
$this->currentStep(); // Returns the current WizardStep instance.
```
```php
$this->nextStep(); // Returns the next WizardStep instance, or null if one does not exist.
```
```php
$this->previousStep(); // Returns the previous WizardStep instance, or null if one does not exist.
```
```php
$this->firstStep(); // Returns the first WizardStep instance.
```
```php
$this->lastStep(); // Returns the last WizardStep instance.
```
```php
$this->step('step-title'); // Returns the WizardStep instance with the given title, or null if it does not exist.
```

## Wizard Navigation Methods
You can use the following methods to navigate between steps in the wizard:

```php
$this->navigateToPreviousStep(); // Sets the current step to the previous step, or does nothing if one does not exist.
```
```php
$this->navigateToNextStep(); // Sets the current step to the next step, or does nothing if one does not exist.
```
```php
$this->navigateToStep('step-title'); // Sets the current step to the WizardStep instance with the given title, or does nothing if it does not exist.
```

## WizardStep Methods
The `WizardStep` class has a number of methods which can be used to render the step in your view. These methods are:

```php
$step->title(); // Returns the title of the step.
```
```php
$step->view(); // Returns the view of the step.
```
```php
$step->is($otherStep); // Returns a boolean value indicating whether the provided WizardStep has the same title as this step.
```
```php
$step->authorise(); // Executes the canNavigate closure and returns the step instance.
```
```php
$step->canNavigate(); // Executes the canNavigate closure and returns a boolean value.
```

## Running Tests
To run the tests, install dependencies:

```bash
composer install
```

Then run the tests using:

```bash
composer test
```

## Credits

- [Sam Watts](https://github.com/sjwatts119)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
