<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Mistybook</title>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}" />
	<link rel="stylesheet" href="{{ asset('backend') }}/css/demo_1/style.css">
	<link rel="stylesheet" href="{{ asset('backend/css/common.css') }}">
</head>
<body>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 m-auto">
                    <div class="card register-main-page">
                        <div class="card-header">
                            <h3>Login</h3>
                        </div>
                        <div class="card-body">
                            @if (session('errorLogin'))
                                <div class="alert alert-warning">{{ session('errorLogin') }}</div>
                            @endif
                            <form action="{{ route('admin.login') }}" class="forms-sample" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email">
                                    @error('email')
                                        <strong class="text text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" autocomplete="current-password" placeholder="Password">
                                    @error('password')
                                        <strong class="text text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                                <div class="form-check form-check-flat form-check-primary">
                                    <a href="">Forget Password</a>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                            <p class="d-block mt-3 text-muted">Don't you have an account? <a href="{{ route('admin.register') }}">Sign in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


	<script src="{{ asset('backend') }}/js/template.js"></script>
</body>
</html>
