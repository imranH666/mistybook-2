@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12 signup-main m-auto">
            <div class="card login" style="background: url({{ asset('assets/signup.jpg') }}) no-repeat center / cover">
                <div class="card-header">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.login') }}" method="POST">
                        @csrf
                        <div class="input-field">
                            <input type="email" name="email" placeholder="Email" />
                            @error('email')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <div class="input-field">
                            <div class="password">
                                <input id="password" type="password" name="password" placeholder="Password" />
                                <img id="togglePassword" src="{{ asset('assets/sleep.png') }}" alt="">
                            </div>
                            @error('password')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <a class="forgor-password" href="{{ route('password.forgot') }}">Forgot Password?</a>
                        <button type="submit" class="btn btn-success">Login</button>
                      </form>
                      <div class="google-login-icon">
                        <h3>OR</h3>
                        <img onclick="window.location.href = '{{ route('google.redirect') }}'" src="{{ asset('assets/google.png') }}" alt="google">
                      </div>
                      <p>Don't you have an account? <a href="{{ route('signup') }}"> <span>Sign up</span> </a></p>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')

    <div class="recent-blog-main">
        <h5>Recent added</h5>
        <div class="recent-blog-slider-list">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach ($recent_blogs as $recent_blog)
                        <div class="swiper-slide">
                            <a href="{{ route('read.blog', $recent_blog->slug) }}">
                                <div>
                                    <img src="{{ asset('upload/blogs') }}/{{ $recent_blog->blog_banner }}" alt="">
                                </div>
                                <h6>{{ Str::limit($recent_blog->blog_title, 50) }}</h6>
                                <h5>{{ $recent_blog->created_at->diffForHumans() }}</h5>
                            </a>
                        </div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
@endsection


@section('footer_script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                const icon = passwordField.getAttribute('type') === 'password'
                    ? '{{ asset('assets/sleep.png') }}'
                    : '{{ asset('assets/eye.png') }}';
                togglePassword.setAttribute('src', icon);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('errorLogin'))
                Toastify({
                    text: "{{ session('errorLogin') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "purple",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('success_login'))
                Toastify({
                    text: "{{ session('success_login') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
                window.location.href = "{{ route('index') }}"
            @endif
            @if(session('updated'))
                Toastify({
                    text: "{{ session('updated') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
            @endif
        });
    </script>
@endsection
