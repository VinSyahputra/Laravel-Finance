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
            <div class="col-md-3">
                <div class="card">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active">Role</a>
                        <a href="#" class="list-group-item list-group-item-action">Permission</a>
                        <a href="#" class="list-group-item list-group-item-action">User</a>
                        <a href="#" class="list-group-item list-group-item-action">Category</a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <button class="btn btn-primary btn-sm ms-auto btn-add-role" data-bs-toggle="modal"
                                data-bs-target="#roleModal"><i class="ti ti-plus"></i> Add Roles</button>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchRoles();
            actionResetModal();
            btnActionSave();
        });

        const actionResetModal = () => {
            $('#roleModal').on('show.bs.modal', function(e) {
                getDataPermissions();
                const triggerButton = $(e.relatedTarget);
                if (triggerButton.hasClass('btn-add-role')) {

                    $('#formRole')[0].reset();
                    $('#formRole input[name="id"]').val('');
                } else {
                    const roleId = $('#formRole input[name="id"]').val();
                    getDataPermissionsByRole(roleId);
                }

            });
        }

        const fetchRoles = (url = '/api/settings/roles', searchQuery = '') => {
            console.log(`Fetching roles from URL: ${url}`);
            if (searchQuery) {
                const querySymbol = url.includes('?') ? '&' : '?';
                url += `${querySymbol}search=${searchQuery}`;
            }
            const userPermissions = @json(auth()->user()->getPermissionsViaRoles()->pluck('name'));

            // console.log(`User Permissions:`, userPermissions);

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
                        if (userPermissions.includes('delete roles')) {
                            deleteButton = `
                        <button type="button" class="btn btn-outline-danger btn-delete-role btn-sm" data-id="${element.id}">
                            <i class="ti ti-trash"></i>
                        </button>`;
                        }

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

                    // // Delete button click handler
                    // $('.btn-delete-role').off('click').on('click', function(e) {
                    //     e.preventDefault();
                    //     const categoryId = $(this).data('id');
                    //     $('#deleteModal').modal('show');
                    //     deleteCategory({
                    //         category_id: categoryId
                    //     });
                    // });

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

                    $('#paginationLinks').html(paginationHtml);
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
                console.log(`Form Data:`, formData);
                let headers = {
                    'Authorization': `Bearer ${authToken}`
                };

                let url = '/api/settings/roles';
                let method = 'POST';

                if (formData.get('id')) {
                    url = `/api/roles/${formData.get('id')}`;
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
                        console.log(`RESPONSE :`, response.status);
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

        function getDataPermissions() {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = '/api/settings/permissions?page=1&per_page=200';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    console.log(response.data);
                    let data = response.data.map(item => ({
                        id: item.id,
                        name: item.name
                    })).sort((a, b) => a.id - b.id);
                    let html = '';
                    data.forEach(item => {
                        html += `
                            <div class="">
                                <div class="form-check">
                                    <input class="form-check-input" name="permissions[]" type="checkbox" value="${item.id}" id="permission_${item.id}">
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
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = `/api/settings/roles/${roleId}?page=1&per_page=200`;
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {

                    let data = response.data.permissions.map(item => ({
                        id: item.id,
                        name: item.name
                    })).sort((a, b) => a.id - b.id);
                    let html = '';
                    data.forEach(item => {
                        html += `
                            <div class="">
                                <div class="form-check">
                                    <input class="form-check-input" name="permissions[]" type="checkbox" value="${item.id}" id="permission_${item.id}">
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
    </script>
@endpush
