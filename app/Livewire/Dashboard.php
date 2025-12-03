<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Monitor;

class Dashboard extends Component
{
    public function render()
    {
        $monitors = Monitor::where('is_active', true)->get();

        return view('livewire.dashboard', [
            'monitors' => $monitors
        ]);
    }
}
