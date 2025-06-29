<?php

namespace App\Livewire\Setting;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Role extends Component
{
    public $user, $data;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        if (!$this->user->hasPermissionTo('view setting role')) {
            abort(403, 'Unauthorized action.');
        }
        return view('livewire.setting.roles');
        // return view('livewire.setting.roles')->extends('layouts.app');
    }
}
