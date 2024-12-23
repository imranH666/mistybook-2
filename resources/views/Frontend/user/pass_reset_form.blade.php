@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card login" style="background: url({{ asset('assets/signup.jpg') }}) no-repeat center / cover">
                <div class="card-header forgot-password-header">
                    <h4>Set a New Password</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('password.reset.update', $token) }}" method="POST">
                        @csrf
                        <div class="input-field">
                            <input type="password" name="password" placeholder="New Password" />
                            @error('password')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <div class="input-field">
                            <input type="password" name="password_confirmation" placeholder="Confirm Password" />
                            @error('password_confirmation')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')
    @include('Frontend.layout.recent_blog_slidebar')
@endsection

