@props(['active'])

<div class="col-md-3">
    <div class="card">
        <div class="list-group list-group-flush">
            <a href="{{ url('/settings/roles') }}" wire:navigate
                class="list-group-item list-group-item-action {{ Request::is('settings/roles*') ? 'active' : '' }}">
                Role
            </a>
            <a href="{{ url('/settings/users') }}" wire:navigate
                class="list-group-item list-group-item-action {{ Request::is('settings/users*') ? 'active' : '' }}">
                User
            </a>
            <a href="{{ url('/settings/categories') }}" wire:navigate
                class="list-group-item list-group-item-action {{ Request::is('settings/categories*') ? 'active' : '' }}">
                Category
            </a>
        </div>
    </div>
</div>
