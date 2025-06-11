@extends('layouts.app')

@section('title', 'User')

@push('styles')
    <!-- Page specific styles -->
    <style>

    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <x-setting-sidebar></x-setting-sidebar>

            <!-- Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <button class="btn btn-primary btn-sm ms-auto btn-add-user" data-bs-toggle="modal"
                                data-bs-target="#userModal"><i class="ti ti-plus"></i> Add Users</button>
                        </div>
                        <div class="d-flex mb-3">
                            <input type="search" class="form-control me-2" id="searchUser" placeholder="Search users...">
                        </div>
                        <table class="table table-sm table-hover mt-3">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th width="1%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyUsers">

                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination" id="paginationUserLinks"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="formUser">
                        <input type="hidden" name="id" id="user_id">
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="user_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="user_name" name="name" autofocus>
                            </div>
                            <div class="col">
                                <label for="user_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="user_email" name="email">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="user_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="user_password" name="password">
                            </div>
                            <div class="col">
                                <label for="user_password_confirm" class="form-label">Password Confirm</label>
                                <input type="password" class="form-control" id="user_password_confirm"
                                    name="password_confirmation">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label for="user_role" class="form-label">Role</label>
                                <select class="form-select" id="user_role" name="role_id">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveUser">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            fetchUsers();
            actionSearch('#searchUser', fetchUsers, '/api/settings/users');
            pagination('#paginationUserLinks', fetchUsers, '#searchUser');
            actionResetModal('#userModal', '#formUser', 'btn-add-user');
            btnActionSave();
            triggerBtnOnEnter('#formUser', '#btnSaveUser');
        });


        function fetchUsers(url = '/api/settings/users', searchQuery = '') {
            if (searchQuery) {
                const querySymbol = url.includes('?') ? '&' : '?';
                url += `${querySymbol}search=${searchQuery}`;
            }

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            };

            $.ajaxSetup({
                headers: headers
            });

            const userPermissions = @json(auth()->user()->getPermissionsViaRoles()->pluck('name'));

            ajaxRequest(url)
                .then(response => {
                    const meta = response.meta;
                    const startIndex = (meta.current_page - 1) * meta.per_page;

                    let htmlContent = '';
                    response.data.forEach((element, index) => {
                        let editButton = '';
                        let deleteButton = '';
                        // if (userPermissions.includes('update users')) {
                        editButton = `
                        <button type="button" class="btn btn-outline-warning btn-update-user btn-sm" 
                                data-id="${element.id}">
                            <i class="ti ti-edit"></i>
                        </button>`;
                        // }
                        // if (userPermissions.includes('delete users')) {
                        deleteButton = `
                        <button type="button" class="btn btn-outline-danger btn-delete-user btn-sm" data-id="${element.id}">
                            <i class="ti ti-trash"></i>
                        </button>`;
                        // }

                        htmlContent += `
                        <tr>    
                            <td>${startIndex + index + 1}</td>
                            <td>${element?.name}</td>
                            <td>${element?.email}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    ${editButton}
                                    ${deleteButton}
                                </div>
                            </td>
                        </tr>`;
                    });

                    $('#tbodyUsers').html(htmlContent);

                    // Delete button click handler
                    $('.btn-delete-user').off('click').on('click', function(e) {
                        console.log(`clicked`);
                        e.preventDefault();
                        const userId = $(this).data('id');
                        $('#deleteUserModal').modal('show');
                        deleteUser({
                            user_id: userId
                        });
                    });

                    // Edit button click handler
                    $('.btn-update-user').off('click').on('click', function(e) {
                        e.preventDefault();
                        const authToken = "{{ session('auth_token') }}";
                        let headers = {
                            'Authorization': `Bearer ${authToken}`,
                            'Accept': 'application/json'
                        };

                        const userId = $(this).data('id');
                        $.ajax({
                            url: `/api/settings/users/${userId}`,
                            type: 'GET',
                            headers: headers,
                            success: function(response) {
                                if (response.status) {
                                    console.log(`User data fetched successfully:`, response
                                        .data);
                                    $('#formUser input[name="name"]').val(response.data.name);
                                    $('#formUser input[name="email"]').val(response.data.email);
                                    $('#formUser input[name="id"]').val(response.data.id);
                                    console.log($('#formUser select[name="role_id"]').html());
                                    $('#formUser select[name="role_id"]').val(response.data.role[0]
                                        .id);
                                }
                            },
                            error: function(error) {
                                showToast(error.responseJSON?.error || 'Failed to update user',
                                    'danger');
                            }
                        });

                        $('#userModal').modal('show');
                    });

                    // Pagination
                    let paginationHtml = '';
                    meta.links.forEach(link => {
                        paginationHtml += `
                    <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-url="${link.url}" ${!link.url ? 'tabindex="-1"' : ''}>
                            ${link.label}
                        </a>
                    </li>`;
                    });

                    $('#paginationUserLinks').html(paginationHtml);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        function btnActionSave() {
            $('#btnSaveUser').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let formData = new FormData($('#formUser')[0]);
                let headers = {
                    'Authorization': `Bearer ${authToken}`
                };

                let url = '/api/settings/users';
                let method = 'POST';

                if (formData.get('id')) {
                    url = `/api/settings/users/${formData.get('id')}`;
                    method = 'POST';
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchUsers();
                            $('#formUser')[0].reset();
                            $('#userModal').modal('hide');
                            showToast('User saved successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON.errors.name[0],
                            'danger');
                    }
                });
            });
        };

        function deleteUser(props) {
            $('#confirmDeleteUserBtn').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let headers = {
                    'Authorization': `Bearer ${authToken}`,
                    'Accept': 'application/json'
                };

                $.ajax({
                    url: `/api/settings/users/${props?.user_id}`,
                    type: 'DELETE',
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchUsers();
                            $('#deleteUserModal').modal('hide');
                            showToast('User deleted successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON?.error || 'Failed to delete user',
                            'danger');
                    }
                });
            });
        };
    </script>
@endpush
