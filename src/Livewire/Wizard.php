<?php

namespace SamWatts\LivewireWizard\Livewire;

use Livewire\Component;

abstract class Wizard extends Component
{
    abstract public function steps(): array;
}
