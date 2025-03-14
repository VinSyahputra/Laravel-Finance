<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <p class="text-center">Register</p>
                                <form action="{{ route('register') }}" method="POST">
                                    @csrf

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="register-name" class="form-label">Name</label>
                                        <input type="name" class="form-control" id="register-name" name="name"
                                            :value="old('name')">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                    <!-- Email Address -->
                                    <div class="mb-3">
                                        <label for="register-email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="register-email" name="email"
                                            :value="old('email')">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label for="register-password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="register-password"
                                            name="password">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-4">
                                        <label for="register-password_confirmation" class="form-label">Password
                                            Confirmation</label>
                                        <input type="password" class="form-control" id="register-password_confirmation"
                                            name="password_confirmation">
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                    </div>

                                    <button type="submit"
                                        class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Register</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                            href="{{ route('login') }}">
                                            {{ __('Already registered?') }}
                                        </a>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
