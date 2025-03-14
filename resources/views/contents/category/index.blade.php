@extends('layouts.app')

@section('title', 'Category')

@push('styles')
    <!-- Page specific styles -->
    <style>

    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!--  Row 1 -->
        <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-header">
                        <h3 class="card-title">Category List</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <button type="button"
                                class="btn btn-primary btn-add-category mx-auto col-12 col-sm-8 col-md-5 mb-3"
                                data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <span>
                                    <i class="ti ti-new-section"></i> add category
                                </span>
                            </button>
                        </div>
                        <div class="d-flex mb-2">
                            <input type="search" class="form-control me-2" id="searchCategory"
                                placeholder="Search categories...">
                        </div>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th>Name</th>
                                    <th width="1%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCategory">
                                <!-- Category rows will be appended here -->
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination" id="paginationLinks"></ul>
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
                        <div class="mb-3">
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchCategories();
            actionResetModal();
            triggerBtnToClick();
            paginateCategory();
            actionSearch();
            btnActionSave();
        });
        const fetchCategories = (url = 'api/categories', searchQuery = '') => {
            // Append search query to the URL
            if (searchQuery) {
                const querySymbol = url.includes('?') ? '&' : '?';
                url += `${querySymbol}search=${searchQuery}`;
            }

            ajaxRequest(url)
                .then(response => {
                    // Calculate the starting index based on the current page
                    const meta = response.meta;
                    const startIndex = (meta.current_page - 1) * meta.per_page;

                    // Populate table rows
                    let htmlContent = '';
                    response.data.forEach((element, index) => {
                        htmlContent += `
                        <tr>    
                          <td>${startIndex + index + 1}</td> <!-- Adjusted index -->
                          <td>${element?.name}</td>
                          <td>
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-outline-warning btn-update-category btn-sm" data-id="${element?.id}" data-name="${element?.name}">
                                <i class="ti ti-edit"></i>
                              </button>
                              <button type="button" class="btn btn-outline-danger btn-delete-category btn-sm" data-id="${element?.id}">
                                <i class="ti ti-trash"></i>
                              </button>
                            </div>
                          </td>

                        </tr>
                      `;
                    });
                    $('#tbodyCategory').html(htmlContent);


                    $('.btn-delete-category').each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();

                            const categoryId = $(this).data('id');



                            $('#deleteModal').modal('show');

                            deleteCategory({
                                category_id: categoryId
                            })
                        });
                    });

                    $('.btn-update-category').each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();

                            const categoryId = $(this).data('id');
                            const categoryName = $(this).data('name');

                            $('#formCategory input[name="id"]').val(categoryId);
                            $('#formCategory input[name="name"]').val(categoryName);

                            $('#categoryModal').modal('show');
                        });
                    });


                    // Handle pagination links
                    const links = meta.links; // Use the updated meta.links for pagination

                    let paginationHtml = '';
                    links.forEach(link => {
                        paginationHtml += `
                          <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                              <a class="page-link" href="#" data-url="${link.url}" ${!link.url ? 'tabindex="-1"' : ''}>
                                  ${link.label}
                              </a>
                          </li>
                      `;
                    });

                    $('#paginationLinks').html(paginationHtml);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };

        const actionResetModal = () => {
            $('#categoryModal').on('show.bs.modal', function(e) {
                const triggerButton = $(e.relatedTarget); // The button that triggered the modal
                if (triggerButton.hasClass('btn-add-category')) {
                    // Clear the form if the modal is opened for adding a new category
                    $('#formCategory')[0].reset(); // Reset the form fields
                    $('#formCategory input[name="id"]').val(''); // Clear the hidden ID field
                }

            });
        }

        $('#categoryModal').on('shown.bs.modal', function(e) {
            $('#category_name').focus();
        });

        const btnActionSave = () => {
            $('#btnSaveCategory').off().on('click', function(e) {
                e.preventDefault();

                let formData = new FormData($('#formCategory')[0]);
                let postData = {};
                formData.forEach(function(value, key) {
                    postData[key] = value;
                });

                if (!postData.id) {
                    console.log('create');
                    // Create new category
                    ajaxRequest('/api/categories', 'POST', postData)
                        .then(response => {
                            if (response.status) {
                                fetchCategories();
                                $('#formCategory')[0].reset();
                                $('#categoryModal').modal('hide');
                                showToast('Category created successfully!', 'success');
                            }
                        })
                        .catch(error => {
                            showToast(error.response.errors, 'danger');
                        });
                } else {
                    console.log('update');
                    // Update existing category
                    ajaxRequest(`/api/categories/${postData.id}`, 'PUT', postData)
                        .then(response => {
                            if (response.status) {
                                fetchCategories();
                                $('#formCategory')[0].reset();
                                $('#categoryModal').modal('hide');
                                showToast('Category updated successfully!', 'success');
                            }
                        })
                        .catch(error => {
                            showToast(error.response.errors, 'danger');
                        });
                }
            });
        }

        const deleteCategory = (props) => {
            $('#confirmDeleteBtn').off().on('click', function(e) {
                ajaxRequest(`/api/categories/${props?.category_id}`, 'DELETE', props)
                    .then(response => {

                        if (response.status) {
                            fetchCategories();
                            $('#deleteModal').modal('hide');
                            showToast('Category deleted successfully!', 'success');
                        }
                    })
                    .catch(error => {
                        showToast(error.response.error, 'danger');
                    });
            })
        }
        const actionSearch = () => {
            // Handle search input
            $('#searchCategory').on('keyup', function() {
                const searchQuery = $(this).val();
                fetchCategories('api/categories', searchQuery); // Fetch with search query
            });
        }

        const triggerBtnToClick = (e) => {
            // Prevent form submission on Enter and trigger the button click event
            $('#formCategory').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent default form submission
                    $('#btnSaveCategory').click(); // Trigger the save button's click event
                }
            });
        }

        const paginateCategory = () => {
            // Handle pagination link clicks
            $('#paginationLinks').on('click', '.page-link', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (url) {
                    const searchQuery = $('#searchCategory').val(); // Get current search value
                    fetchCategories(url, searchQuery);
                }
            });
        }
    </script>
@endpush
