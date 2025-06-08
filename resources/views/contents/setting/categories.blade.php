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
                            <button class="btn btn-primary btn-sm ms-auto btn-add-category" data-bs-toggle="modal"
                                data-bs-target="#categoryModal"><i class="ti ti-plus"></i> Add Categories</button>
                        </div>
                        <div class="d-flex mb-3">
                            <input type="search" class="form-control me-2" id="searchCategory"
                                placeholder="Search categories...">
                        </div>
                        <table class="table table-sm table-hover mt-3">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th>Category Name</th>
                                    <th width="1%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCategories">

                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination" id="paginationCategoryLinks"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="formCategory">
                        <input type="hidden" name="id" id="category_id">
                        <div class="col mb-3">
                            <label for="category_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="category_name" name="name" autofocus>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveCategory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this category?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCategoryBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchCategories();
            actionResetModal('#categoryModal', '#formCategory', 'btn-add-category');
            btnActionSave();
            pagination('#paginationCategoryLinks',fetchCategories, '#searchCategory');
            actionSearch('#searchCategory', fetchCategories, '/api/settings/categories');
            triggerBtnOnEnter('#formCategory', '#btnSaveCategory');
        });

        
        $('#categoryModal').on('shown.bs.modal', function(e) {
            $('#category_name').focus();
        });

        const fetchCategories = (url = '/api/settings/categories', searchQuery = '') => {
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
                        // if (userPermissions.includes('update categories')) {
                        editButton = `
                        <button type="button" class="btn btn-outline-warning btn-update-category btn-sm" 
                                data-id="${element.id}">
                            <i class="ti ti-edit"></i>
                        </button>`;
                        // }
                        // if (userPermissions.includes('delete categories')) {
                        deleteButton = `
                        <button type="button" class="btn btn-outline-danger btn-delete-category btn-sm" data-id="${element.id}">
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

                    $('#tbodyCategories').html(htmlContent);

                    // Delete button click handler
                    $('.btn-delete-category').off('click').on('click', function(e) {
                        console.log(`clicked`);
                        e.preventDefault();
                        const categoryId = $(this).data('id');
                        $('#deleteCategoryModal').modal('show');
                        deleteCategory({
                            category_id: categoryId
                        });
                    });

                    // Edit button click handler
                    $('.btn-update-category').off('click').on('click', function(e) {
                        e.preventDefault();
                        const authToken = "{{ session('auth_token') }}";
                        let headers = {
                            'Authorization': `Bearer ${authToken}`,
                            'Accept': 'application/json'
                        };

                        const categoryId = $(this).data('id');
                        $.ajax({
                            url: `/api/settings/categories/${categoryId}`,
                            type: 'GET',
                            headers: headers,
                            success: function(response) {
                                if (response.status) {
                                    console.log(`Category data fetched successfully:`, response
                                        .data);
                                    $('#formCategory input[name="name"]').val(response.data
                                        .name);
                                    $('#formCategory input[name="email"]').val(response.data
                                        .email);
                                    $('#formCategory input[name="id"]').val(response.data.id);
                                    console.log($('#formCategory select[name="role_id"]')
                                        .html());
                                    $('#formCategory select[name="role_id"]').val(response.data
                                        .role[0].id);
                                }
                            },
                            error: function(error) {
                                showToast(error.responseJSON?.error ||
                                    'Failed to update category',
                                    'danger');
                            }
                        });

                        $('#categoryModal').modal('show');
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

                    $('#paginationCategoryLinks').html(paginationHtml);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        const btnActionSave = () => {
            $('#btnSaveCategory').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let formData = new FormData($('#formCategory')[0]);
                let headers = {
                    'Authorization': `Bearer ${authToken}`
                };

                let url = '/api/settings/categories';
                let method = 'POST';

                if (formData.get('id')) {
                    url = `/api/settings/categories/${formData.get('id')}`;
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
                            fetchCategories();
                            $('#formCategory')[0].reset();
                            $('#categoryModal').modal('hide');
                            showToast('Category saved successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON.errors.name[0],
                            'danger');
                    }
                });
            });
        };

        const deleteCategory = (props) => {
            $('#confirmDeleteCategoryBtn').off().on('click', function(e) {
                e.preventDefault();
                const authToken = "{{ session('auth_token') }}";
                let headers = {
                    'Authorization': `Bearer ${authToken}`,
                    'Accept': 'application/json'
                };

                $.ajax({
                    url: `/api/settings/categories/${props?.category_id}`,
                    type: 'DELETE',
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchCategories();
                            $('#deleteCategoryModal').modal('hide');
                            showToast('Category deleted successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON?.error || 'Failed to delete category',
                            'danger');
                    }
                });
            });
        };
    </script>
@endpush
