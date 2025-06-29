<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Category extends Component
{
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        if (!$this->user->hasPermissionTo('view setting category')) {
            abort(403, 'Unauthorized action.');
        }
        return view('livewire.setting.categories');
    }
}
