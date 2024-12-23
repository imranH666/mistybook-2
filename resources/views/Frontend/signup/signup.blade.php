@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12 signup-main m-auto">
            <div class="card signup" style="background: url({{ asset('assets/signup.jpg') }}) no-repeat center / cover">
                <div class="card-header">
                    <h4>Sign Up</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.signup') }}" method="POST">
                        @csrf
                        <div class="input-field">
                            <input type="text" name="fname" value="{{ old('fname') }}" placeholder="First Name" />
                            @error('fname')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <div class="input-field">
                            <input type="text" name="lname" value="{{ old('lname') }}" placeholder="Last Name" />
                        </div>
                        <div class="input-field">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" />
                            @error('email')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <div class="input-field">
                            <div class="password">
                                <input id="first_password" type="password" name="password" placeholder="Password" />
                                <img id="togglePassword" src="{{ asset('assets/sleep.png') }}" alt="">
                            </div>
                            @error('password')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <div class="input-field">
                            <div class="password">
                                <input id="confirm_password" type="password" name="password_confirmation" placeholder="Confirm Password" />
                                <img id="togglePassword2" src="{{ asset('assets/sleep.png') }}" alt="">
                            </div>
                            @error('password_confirmation')
                                <strong class="text-danger">{{$message}}</strong>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success sign-up-btn">Sign up</button>
                    </form>
                      <p>Already have an account? <a href="{{ route('login') }}"> <span>Login</span> </a></p>
                </div>
                <div class="signup-next-step">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="signup-next-container">
                            <h5>@lang('messages.which-categories-interested-reading')</h5>
                            <div class="row">
                                @foreach (App\Models\Category::all() as $category)
                                    <div class="col-lg-6">
                                        <input class="form-check-input" type="checkbox" name="categories[]" id="category-{{ $category->id }}" value="{{ $category->id }}">
                                        <label  class="form-check-label"for="category-{{ $category->id }}">{{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="signup-next-step-btns">
                                <button onclick="window.location.href = '{{ route('index') }}'" type="button">Skip</button>
                                <button type="submit">Continue</button>
                            </div>
                        </div>
                    </form>
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
            const passwordField = document.getElementById('first_password');
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
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('confirm_password');
            const togglePassword = document.getElementById('togglePassword2');
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
            @if(session('existEmail'))
                Toastify({
                    text: "{{ session('existEmail') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('success_signup'))
                Toastify({
                    text: "{{ session('success_signup') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
                // window.location.href = "{{ route('index') }}"
                $('.signup-next-step').css('transform', 'translate(-50%, 65%)')
                $('.sign-up-btn').css('display', 'none')
                $('.signup-next-container').css('display', 'block')
            @endif
            @if($errors->has('categories'))
                Toastify({
                    text: "{{ $errors->first('categories') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('no_user'))
                Toastify({
                    text: "{{ session('no_user') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "#eec271",
                    stopOnFocus: true,
                }).showToast();
            @endif
        });
    </script>
@endsection
