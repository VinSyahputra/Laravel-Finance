@extends('layouts.app')

@section('title', 'Category')

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
                            <button class="btn btn-primary btn-sm ms-auto btn-add-role" data-bs-toggle="modal"
                                data-bs-target="#roleModal"><i class="ti ti-plus"></i> Add Roles</button>
                        </div>
                        <div class="d-flex mb-3">
                            <input type="search" class="form-control me-2" id="searchRole"
                                placeholder="Search roles...">
                        </div>
                        <table class="table table-sm table-hover mt-3">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th>Role Name</th>
                                    <th width="1%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRoles">

                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination" id="paginationRoleLinks"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="formRole">
                        <input type="hidden" name="id" id="role_id">
                        <div class="mb-3">
                            <label for="role_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="role_name" name="role_name" autofocus>
                        </div>
                        <div class="d-flex justify-content-end mb-3 gap-2">
                            <button type="button" class="btn btn-primary btn-sm" id="btn_permission_select_all">Select
                                All</button>
                            <button type="button" class="btn btn-light btn-sm" id="btn_permission_unselect_all">Unselect
                                All</button>
                        </div>
                        <div class="mb-3">
                            <label for="role_permissions" class="form-label">Permissions</label>
                            <div class="" id="containerPermission" style="max-height: 150px; overflow-y: auto;">


                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveRole">Save</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Delete Confirmation Modal -->
     <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRoleBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchRoles();
            actionResetModal('#roleModal', '#formRole', 'btn-add-role');
            btnActionSave();
            actionSearch('#searchRole', fetchRoles, '/api/settings/roles');
            pagination('#paginationRoleLinks',fetchRoles, '#searchRole');
            triggerBtnOnEnter('#formRole', '#btnSaveRole');
        });

        

        const fetchRoles = (url = '/api/settings/roles', searchQuery = '') => {
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
                        // if (userPermissions.includes('update roles')) {
                        editButton = `
                        <button type="button" class="btn btn-outline-warning btn-update-role btn-sm" 
                                data-id="${element.id}" data-name="${element.name}">
                            <i class="ti ti-edit"></i>
                        </button>`;
                        // }
                        // if (userPermissions.includes('delete roles')) {
                            deleteButton = `
                        <button type="button" class="btn btn-outline-danger btn-delete-role btn-sm" data-id="${element.id}">
                            <i class="ti ti-trash"></i>
                        </button>`;
                        // }

                        htmlContent += `
                        <tr>    
                            <td>${startIndex + index + 1}</td>
                            <td>${element?.name}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    ${editButton}
                                    ${deleteButton}
                                </div>
                            </td>
                        </tr>`;
                    });

                    $('#tbodyRoles').html(htmlContent);

                    // Delete button click handler
                    $('.btn-delete-role').off('click').on('click', function(e) {
                        e.preventDefault();
                        const roleId = $(this).data('id');
                        $('#deleteRoleModal').modal('show');
                        deleteRole({
                            role_id: roleId
                        });
                    });

                    // Edit button click handler
                    $('.btn-update-role').off('click').on('click', function(e) {
                        e.preventDefault();
                        const roleId = $(this).data('id');
                        const roleName = $(this).data('name');

                        $('#formRole input[name="id"]').val(roleId);
                        $('#formRole input[name="role_name"]').val(roleName);

                        $('#roleModal').modal('show');
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

                    $('#paginationRoleLinks').html(paginationHtml);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        $('#btn_permission_select_all').on('click', function() {
            $('#containerPermission input[type="checkbox"]').prop('checked', true);
        });
        $('#btn_permission_unselect_all').on('click', function() {
            $('#containerPermission input[type="checkbox"]').prop('checked', false);
        });

        const btnActionSave = () => {
            $('#btnSaveRole').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let formData = new FormData($('#formRole')[0]);
                let headers = {
                    'Authorization': `Bearer ${authToken}`
                };

                let url = '/api/settings/roles';
                let method = 'POST';

                if (formData.get('id')) {
                    url = `/api/settings/roles/${formData.get('id')}`;
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
                            fetchRoles();
                            $('#formRole')[0].reset();
                            $('#roleModal').modal('hide');
                            showToast('Role saved successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON.errors.name[0],
                            'danger');
                    }
                });
            });
        };

        function getDataPermissions(parram = []) {
            console.log(`parram`, parram);
            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' 
            };

            let url = '/api/settings/permissions?page=1&per_page=200';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    const parramIds = parram.map(item => item.id);

                    let data = response.data.map(item => ({
                        id: item.id,
                        name: item.name,
                        checked: parramIds.includes(item.id)
                    })).sort((a, b) => a.id - b.id);
                    let html = '';
                    console.log(`data`, data);
                    data.forEach(item => {
                        html += `
                            <div class="">
                                <div class="form-check">
                                    <input class="form-check-input" name="permissions[]" type="checkbox" value="${item.id}" id="permission_${item.id}"  ${item.checked ? 'checked' : ''}>
                                    <label class="form-check-label" for="permission_${item.id}">
                                        ${item.name}
                                    </label>
                                </div>
                            </div>
                    `;
                    });
                    $('#containerPermission').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        function getDataPermissionsByRole(roleId) {
            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            };

            let url = `/api/settings/roles/${roleId}?page=1&per_page=200`;

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: headers,
                    success: function(response) {
                        let data = response.data.permissions.map(item => ({
                            id: item.id,
                            name: item.name
                        })).sort((a, b) => a.id - b.id);

                        resolve(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        reject(error);
                    }
                });
            });
        }

        const deleteRole = (props) => {
            $('#confirmDeleteRoleBtn').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let headers = {
                    'Authorization': `Bearer ${authToken}`,
                    'Accept': 'application/json'
                };

                $.ajax({
                    url: `/api/settings/roles/${props?.role_id}`,
                    type: 'DELETE',
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchRoles();
                            $('#deleteRoleModal').modal('hide');
                            showToast('Role deleted successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON?.error || 'Failed to delete role',
                            'danger');
                    }
                });
            });
        };

    </script>
@endpush
