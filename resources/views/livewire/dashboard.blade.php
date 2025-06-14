@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!--  Row 1 -->
        <div class="row">
            <div class="col-lg-8 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                            <div class="mb-3 mb-sm-0">
                                <h5 class="card-title fw-semibold">Sales Overview</h5>
                            </div>
                            <div>
                                <select class="form-select">
                                    <option value="1">March 2023</option>
                                    <option value="2">April 2023</option>
                                    <option value="3">May 2023</option>
                                    <option value="4">June 2023</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Yearly Breakup -->
                        <div class="card overflow-hidden">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-9 fw-semibold">Yearly Breakup</h5>
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="fw-semibold mb-3">$36,358</h4>
                                        <div class="d-flex align-items-center mb-3">
                                            <span
                                                class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-arrow-up-left text-success"></i>
                                            </span>
                                            <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                                            <p class="fs-3 mb-0">last year</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="me-4">
                                                <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                                <span class="fs-2">2023</span>
                                            </div>
                                            <div>
                                                <span
                                                    class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                                                <span class="fs-2">2023</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-center">
                                            <div id="breakup"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <!-- Monthly Earnings -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row alig n-items-start">
                                    <div class="col-8">
                                        <h5 class="card-title mb-9 fw-semibold"> Monthly Earnings </h5>
                                        <h4 class="fw-semibold mb-3">$6,820</h4>
                                        <div class="d-flex align-items-center pb-1">
                                            <span
                                                class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-arrow-down-right text-danger"></i>
                                            </span>
                                            <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                                            <p class="fs-3 mb-0">last year</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex justify-content-end">
                                            <div
                                                class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-currency-dollar fs-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Recent Transactions</h5>
                        <div class="table-responsive">
                            <table class="table text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">No</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Input By</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Description</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Category</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Amount</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="dashboardBodyRecentTrans">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    {{-- <script src="{{ asset('assets/js/dashboard.js') }}"></script> --}}

    <script>
        document.addEventListener('livewire:navigated', function() {
            // fetchRecentTransactions();
        });

        function fetchRecentTransactions() {
            const authToken = "{{ session('auth_token') }}";

            let headers = {
                'Authorization': `Bearer ${authToken}`
            };

            let url = '/api/analytics/recent-transactions';
            let method = 'GET';

            $.ajax({
                url: url,
                type: method,
                processData: false,
                contentType: false,
                headers: headers,
                success: function(response) {
                    if (response.status) {
                        let htmlContent = '';
                        response.data.forEach((element, index) => {
                            htmlContent += `
                        <tr>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">${index+1}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-1">${element?.user?.name}</h6>
                            </td>
                            <td class="border-bottom-0">
                                <p class="mb-0 fw-normal">${element?.description}</p>
                            </td>
                            <td class="border-bottom-0">
                                <p class="mb-0 fw-normal">${element?.category?.name}</p>
                            </td>
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0 fs-4">Rp. ${Number(element?.amount).toLocaleString('id-ID')}</h6>
                            </td>
                        </tr>`;
                        });

                        $('#dashboardBodyRecentTrans').html(htmlContent);
                    }
                },
                error: function(error) {
                    let message = 'Something went wrong';

                    if (error?.responseJSON?.errors) {
                        const errors = error.responseJSON.errors;
                        const firstKey = Object.keys(errors)[0];
                        message = errors[firstKey][0];
                    } else if (error?.responseJSON?.message) {
                        message = error.responseJSON.message;
                    }

                    showToast(message, 'danger');
                }
            });
        };
    </script>
@endpush
