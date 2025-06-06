@extends('layouts.app')

@section('title', 'Transaction')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-9 fw-semibold">Yearly Expenses</h5>
                        <div class="row align-items-center" id="card_container_expense_yearly">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-9 fw-semibold">Monthly Expenses</h5>
                        <div class="row align-items-center" id="card_container_expense_monthly">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-9 fw-semibold">Yearly Incomes</h5>
                        <div class="row align-items-center" id="card_container_income_yearly">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-9 fw-semibold">Monthly Incomes</h5>
                        <div class="row align-items-center" id="card_container_income_monthly">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="d-flex">
                <button type="button" class="btn btn-primary mx-auto col-12 col-sm-8 col-md-5 mb-3" id="addTransaction">
                    <span>
                        <i class="ti ti-plus"></i> Add Transaction
                    </span>
                </button>
            </div>
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-header">
                        <h3 class="card-title">Transaction List</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <!-- Search Input -->
                            <input type="search" class="form-control me-2" id="searchTransaction"
                                placeholder="Search transactions...">

                            <select class="form-select me-2" id="filterYear">
                                <option value="">Select Year</option>
                            </select>

                            <!-- Month Filter -->
                            <select class="form-select me-2" id="filterMonth">
                                <option value="">Select Month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>

                            <!-- Categories Filter -->
                            <select class="form-select" id="filterCategory"></select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th width="1%">No.</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th width="1%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyTransaction">
                                    <!-- Category rows will be appended here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination" id="paginationTransactionLinks"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="formTransaction" class="row">
                        <input type="hidden" name="id" id="transaction_id">
                        <!-- Date Field -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="transaction_date" name="date">
                        </div>



                        <!-- Category Field -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_category" class="form-label">Category</label>
                            <select class="form-select" id="transaction_category" name="category_id">
                                <!-- Add more categories as needed -->
                            </select>
                        </div>

                        <!-- Description Field -->
                        <div class="col-md-12 mb-3">
                            <label for="transaction_description" class="form-label">Description</label>
                            <textarea class="form-control" id="transaction_description" name="description" rows="4"></textarea>
                        </div>

                        <!-- Type Field -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_type" class="form-label">Type</label>
                            <select class="form-select" id="transaction_type" name="type">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>

                        <!-- Amount Field -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_amount" class="form-label">Amount</label>
                            <input type="text" class="form-control" id="transaction_amount" name="amount"
                                step="0.01" oninput="formatPrice(this)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveTransaction">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-labelledby="deleteTransactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTransactionModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmTransactionDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            getDataExpenseInYear();
            getDataIncomeInYear();
            getDataExpenseInMonth();
            getDataIncomeInMonth();
            populateYearSelect();
            getCategories('filterCategory');
            getCategories('transaction_category');
            fetchTransactions();
            btnActionSaveTransaction();

            $('#addTransaction').on('click', e => {
                $('#formTransaction')[0].reset();
                $('#transaction_id').val('');
                $('#transactionModal').modal('show');
            })
        });


        const fetchTransactions = (url = 'api/transactions', searchQuery = '', categoryId = '', month = '', year = '') => {
            // Append search query to the URL
            const params = new URLSearchParams();
            let userId = <?= json_encode($user->id) ?>;

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            };

            $.ajaxSetup({
                headers: headers
            });
            
            params.append('user_id', userId);

            if (searchQuery) params.append('search', searchQuery); // Search query
            if (categoryId) params.append('category_id', categoryId); // Category filter
            if (month) params.append('month', month); // Month filter
            if (year) params.append('year', year); // Year filter

            if (params.toString()) {
                const querySymbol = url.includes('?') ? '&' : '?';
                url += `${querySymbol}${params.toString()}`;
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
                          <td>${moment(element?.date).format('DD/MMM/YYYY')}</td>
                          <td>${element?.description}</td>
                          <td>${element?.category?.name || 'No Category'}</td>
                          <td>Rp. ${formatPrice(element?.amount ?? 0)}</td>
                          <td>
                           <span class="badge rounded-3 fw-semibold pt-1 ${element?.type == 'income' ? 'bg-success' : 'bg-danger '}">${element?.type}</span>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-outline-warning btn-update-transaction btn-sm" data-id="${element?.id}" data-transaction="${btoa(JSON.stringify(element))}">
                                <i class="ti ti-edit"></i>
                              </button>
                              <button type="button" class="btn btn-outline-danger btn-delete-transaction btn-sm" data-id="${element?.id}">
                                <i class="ti ti-trash"></i>
                              </button>
                            </div>
                          </td>

                        </tr>
                      `;
                    });
                    $('#tbodyTransaction').html(htmlContent);

                    $('.btn-delete-transaction').each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();
                            const transactionId = $(this).data('id');

                            $('#deleteTransactionModal').modal('show');

                            deleteTransaction({
                                transaction_id: transactionId
                            })
                        });
                    });

                    $('.btn-update-transaction').each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();
                            console.log(`clicked update`);
                            const transaction = JSON.parse(atob($(this).data('transaction')));

                            $('#transactionModal').modal('show');

                            // Handle date formatting for the date field
                            if (transaction?.date) {
                                const formattedDate = new Date(transaction.date).toISOString()
                                    .split('T')[0]; // Convert to YYYY-MM-DD
                                $('#formTransaction input[name="date"]').val(formattedDate);
                            }

                            $('#formTransaction input[name="id"]').val(transaction?.id);
                            $('#formTransaction textarea[name="description"]').val(transaction
                                ?.description);
                            $('#formTransaction input[name="amount"]').val(
                                transaction?.amount);

                            $('#formTransaction select[name="category_id"]').val(transaction
                                ?.category_id).change();
                            $('#formTransaction select[name="type"]').val(transaction
                                ?.type).change();
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

                    $('#paginationTransactionLinks').html(paginationHtml);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        };


        // Handle pagination link clicks
        $('#paginationTransactionLinks').on('click', '.page-link', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            if (url) {
                const searchQuery = $('#searchTransaction').val();
                const categoryId = $('#filterCategory').val();
                const month = $('#filterMonth').val();
                const year = $('#filterYear').val();

                fetchTransactions(url, searchQuery, categoryId, month, year);
            }
        });


        function populateYearSelect() {
            const currentYear = new Date().getFullYear();
            const $selectElement = $('#filterYear');

            // Clear previous options
            $selectElement.empty();

            // Add the default option
            $selectElement.append('<option value="">Select Year</option>');

            // Loop to create options from 5 years ago to 5 years later
            for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                // Append each year as an option
                $selectElement.append(`<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`);
            }
        }

        function getDataExpenseInYear() {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = '/api/analytics/expense-this-year';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    let statusClass = 'secondary';
                    let iconClass = 'ti ti-arrows-left-right text-secondary'; // Default neutral icon
                    let prefix = '';

                    if (response?.data?.status === 'high') {
                        statusClass = 'danger';
                        iconClass = 'ti ti-arrow-up-left text-danger';
                    } else if (response?.data?.status === 'low') {
                        statusClass = 'success';
                        iconClass = 'ti ti-arrow-down-right text-success';
                    }

                    const totalAmount = Number(response?.data?.total_amount) || 0;
                    const formattedAmount = totalAmount.toLocaleString('id-ID');

                    let htmlContent = `
                        <div class="col-8">
                            <h4 class="fw-semibold mb-3">RP. ${formattedAmount}</h4>
                            <div class="d-flex align-items-center mb-3">
                                <span class="me-1 rounded-circle bg-light-${statusClass} round-20 d-flex align-items-center justify-content-center">
                                    <i class="${iconClass}"></i>
                                </span>
                                <p class="text-dark me-1 fs-3 mb-0"> ${response?.data?.percentage}%</p>
                                <p class="fs-3 mb-0">last year</p>
                            </div>
                        </div>
                    `;

                    $(`#card_container_expense_yearly`).html(htmlContent);
                },
                error: function(error) {
                    showToast(error.responseJSON?.errors?.[Object.keys(error.responseJSON
                        .errors)[0]][0] || 'Failed to save transaction', 'danger');
                }
            });
        }

        function getDataIncomeInYear() {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = '/api/analytics/income-this-year';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    let statusClass = 'secondary';
                    let iconClass = 'ti ti-arrows-left-right text-secondary'; // Default neutral icon
                    let prefix = '';

                    if (response?.data?.status === 'high') {
                        statusClass = 'success';
                        iconClass = 'ti ti-arrow-up-left text-success';
                    } else if (response?.data?.status === 'low') {
                        statusClass = 'danger';
                        iconClass = 'ti ti-arrow-down-right text-danger';
                    }

                    const totalAmount = Number(response?.data?.total_amount) || 0;
                    const formattedAmount = totalAmount.toLocaleString('id-ID');

                    let htmlContent = `
                        <div class="col-8">
                            <h4 class="fw-semibold mb-3">RP. ${formattedAmount}</h4>
                            <div class="d-flex align-items-center mb-3">
                                <span class="me-1 rounded-circle bg-light-${statusClass} round-20 d-flex align-items-center justify-content-center">
                                    <i class="${iconClass}"></i>
                                </span>
                                <p class="text-dark me-1 fs-3 mb-0"> ${response?.data?.percentage}%</p>
                                <p class="fs-3 mb-0">last year</p>
                            </div>
                        </div>
                    `;

                    $(`#card_container_income_yearly`).html(htmlContent);
                },
                error: function(error) {
                    showToast(error.responseJSON?.errors?.[Object.keys(error.responseJSON
                        .errors)[0]][0] || 'Failed to save transaction', 'danger');
                }
            });
        }

        function getDataExpenseInMonth() {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = '/api/analytics/expense-this-month';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    let statusClass = 'secondary';
                    let iconClass = 'ti ti-arrows-left-right text-secondary'; // Default neutral icon
                    let prefix = '';

                    if (response?.data?.status === 'high') {
                        statusClass = 'danger';
                        iconClass = 'ti ti-arrow-up-left text-danger';
                    } else if (response?.data?.status === 'low') {
                        statusClass = 'success';
                        iconClass = 'ti ti-arrow-down-right text-success';
                    }

                    const totalAmount = Number(response?.data?.total_amount) || 0;
                    const formattedAmount = totalAmount.toLocaleString('id-ID');

                    let htmlContent = `
                        <div class="col-8">
                            <h4 class="fw-semibold mb-3">RP. ${formattedAmount}</h4>
                            <div class="d-flex align-items-center mb-3">
                                <span class="me-1 rounded-circle bg-light-${statusClass} round-20 d-flex align-items-center justify-content-center">
                                    <i class="${iconClass}"></i>
                                </span>
                                <p class="text-dark me-1 fs-3 mb-0"> ${response?.data?.percentage}%</p>
                                <p class="fs-3 mb-0">last month</p>
                            </div>
                        </div>
                    `;

                    $(`#card_container_expense_monthly`).html(htmlContent);
                },
                error: function(error) {
                    showToast(error.responseJSON?.errors?.[Object.keys(error.responseJSON
                        .errors)[0]][0] || 'Failed to save transaction', 'danger');
                }
            });
        }

        function getDataIncomeInMonth() {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json' // Ensure Laravel returns JSON
            };

            let url = '/api/analytics/income-this-month';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                headers: headers,
                success: function(response) {
                    let statusClass = 'secondary';
                    let iconClass = 'ti ti-arrows-left-right text-secondary'; // Default neutral icon
                    let prefix = '';

                    if (response?.data?.status === 'high') {
                        statusClass = 'success';
                        iconClass = 'ti ti-arrow-up-left text-success';
                    } else if (response?.data?.status === 'low') {
                        statusClass = 'danger';
                        iconClass = 'ti ti-arrow-down-right text-danger';
                    }

                    const totalAmount = Number(response?.data?.total_amount) || 0;
                    const formattedAmount = totalAmount.toLocaleString('id-ID');

                    let htmlContent = `
                        <div class="col-8">
                            <h4 class="fw-semibold mb-3">RP. ${formattedAmount}</h4>
                            <div class="d-flex align-items-center mb-3">
                                <span class="me-1 rounded-circle bg-light-${statusClass} round-20 d-flex align-items-center justify-content-center">
                                    <i class="${iconClass}"></i>
                                </span>
                                <p class="text-dark me-1 fs-3 mb-0"> ${response?.data?.percentage}%</p>
                                <p class="fs-3 mb-0">last month</p>
                            </div>
                        </div>
                    `;

                    $(`#card_container_income_monthly`).html(htmlContent);
                },
                error: function(error) {
                    showToast(error.responseJSON?.errors?.[Object.keys(error.responseJSON
                        .errors)[0]][0] || 'Failed to save transaction', 'danger');
                }
            });
        }

        function getCategories(container) {
            ajaxRequest(`/api/categories`, 'GET')
                .then(response => {
                    $(`#${container}`).empty();
                    $(`#${container}`).append('<option value="">Select Category</option>');

                    let htmlContent = '';
                    if (response.data.length > 0) {
                        response.data.forEach(each => {
                            htmlContent += `
                  <option value="${each?.id}">${each?.name}</option>
                `;
                        });
                        $(`#${container}`).append(htmlContent)
                    }
                })
                .catch(error => {

                });
        }

        function getDataByMonth() {
            ajaxRequest(`/api/categories`, 'GET')
                .then(response => {
                    $(`#${container}`).empty();
                    $(`#${container}`).append('<option value="">Select Category</option>');

                    let htmlContent = '';
                    if (response.data.length > 0) {
                        response.data.forEach(each => {
                            htmlContent += `
                  <option value="${each?.id}">${each?.name}</option>
                `;
                        });
                        $(`#${container}`).append(htmlContent)
                    }
                })
                .catch(error => {

                });
        }
        const btnActionSaveTransaction = () => {
            $('#btnSaveTransaction').off().on('click', function(e) {
                e.preventDefault();

                let formData = new FormData($('#formTransaction')[0]); // Use FormData directly
                formData.append('user_id', <?= json_encode($user->id) ?>); // Add user_id
                const authToken = "{{ session('auth_token') }}";
                let headers = {
                    'Authorization': `Bearer ${authToken}`,
                    'Accept': 'application/json' // Ensure Laravel returns JSON
                };

                let url = '/api/transactions';
                let method = 'POST';

                if (formData.get('id')) {
                    url = `/api/transactions/${formData.get('id')}`;
                    method = 'POST'; // Laravel method spoofing
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false, // Required for FormData
                    contentType: false, // Required for FormData
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchTransactions();
                            getDataExpenseInYear();
                            getDataIncomeInYear();
                            getDataExpenseInMonth();
                            getDataIncomeInMonth();
                            $('#formTransaction')[0].reset();
                            $('#transactionModal').modal('hide');
                            showToast(
                                `Transaction ${method === 'POST' ? 'created' : 'updated'} successfully!`,
                                'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON?.errors?.[Object.keys(error.responseJSON
                            .errors)[0]][0] || 'Failed to save transaction', 'danger');
                    }
                });
            });

            // Reset form when modal is opened for adding a new transaction
            // $('#transactionModal').on('show.bs.modal', function(event) {
            //     let button = $(event.relatedTarget);
            //     let transactionId = button.data('id');
            //     if (!transactionId) {
            //         console.log(button);
            //         $('#formTransaction')[0].reset();
            //     }
            // });

            // KETIKA BUKA MODAL SAAT EDIT RESET FORM KETIKA BUKA LAGI MODAL SAAT TAMBAH

        };


        $('#searchTransaction, #filterCategory, #filterMonth, #filterYear').on('input change', function() {
            const searchQuery = $('#searchTransaction').val(); // Search query
            const categoryId = $('#filterCategory').val(); // Category ID
            const month = $('#filterMonth').val(); // Selected month
            const year = $('#filterYear').val(); // Selected year

            fetchTransactions('api/transactions', searchQuery, categoryId, month, year);
        });

        const deleteTransaction = (props) => {

            const authToken = "{{ session('auth_token') }}";
            let headers = {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            };

            $('#confirmTransactionDeleteBtn').off().on('click', function(e) {

                $.ajax({
                    url: `/api/transactions/${props?.transaction_id}`,
                    type: 'DELETE',
                    headers: headers,
                    success: function(response) {
                        if (response.status) {
                            fetchTransactions();
                            getDataExpenseInYear();
                            getDataIncomeInYear();
                            getDataExpenseInMonth();
                            getDataIncomeInMonth();
                            $('#deleteTransactionModal').modal('hide');
                            showToast('Transaction deleted successfully!', 'success');
                        }
                    },
                    error: function(error) {
                        showToast(error.responseJSON?.error || 'Failed to delete category',
                            'danger');
                    }
                });
            })
        };
    </script>
@endpush
