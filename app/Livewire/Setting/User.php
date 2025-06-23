<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class User extends Component
{
    public $user, $roles;

    public function mount()
    {
        $this->user = Auth::user();
        $this->roles = Role::get(['name', 'id']);
    }

    public function render()
    {
        if (!$this->user->hasPermissionTo('view setting user')) {
            abort(403, 'Unauthorized action.');
        }
        return view('livewire.setting.users');
    }
}
