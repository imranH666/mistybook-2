@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card login" style="background: url({{ asset('assets/signup.jpg') }}) no-repeat center / cover">
                <div class="card-header forgot-password-header">
                    <h4>Forgot Password</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('password.forgot.req.send') }}" method="POST">
                        @csrf
                        <div class="input-field">
                            <input type="email" name="email" placeholder="Email" />
                            @error('email')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success">Send Request</button>
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


@section('footer_script')
    <script>
        @if(session('invalid'))
            Toastify({
                text: "{{ session('invalid') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection
