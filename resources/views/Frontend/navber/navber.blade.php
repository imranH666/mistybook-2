<!DOCTYPE html>
{{-- <html lang="en"> --}}
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mistybook</title>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('style/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/lightbox.css') }}">
    <link rel="stylesheet" href="{{ asset('style/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('style/home.css') }}">
    <link rel="stylesheet" href="{{ asset('style/category.css') }}">
    <link rel="stylesheet" href="{{ asset('style/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('style/login.css') }}">
    <link rel="stylesheet" href="{{ asset('style/signup.css') }}">
    <link rel="stylesheet" href="{{ asset('style/post.css') }}">
    <link rel="stylesheet" href="{{ asset('style/notification.css') }}">
    <link rel="stylesheet" href="{{ asset('style/question-answer.css') }}">
    <link rel="stylesheet" href="{{ asset('style/favourite.css') }}">
    <link rel="stylesheet" href="{{ asset('style/blog.css') }}">
    <link rel="stylesheet" href="{{ asset('style/friend.css') }}">
    <link rel="stylesheet" href="{{ asset('style/video.css') }}">
    <link rel="stylesheet" href="{{ asset('style/chat.css') }}">
    <link rel="stylesheet" href="{{ asset('style/setting.css') }}">
    <link rel="stylesheet" href="{{ asset('style/explor.css') }}">
    <link rel="stylesheet" href="{{ asset('style/support.css') }}">
    <link rel="stylesheet" href="{{ asset('style/responsive.css') }}">
    <style>
        .lb-close {
            background: url({{ asset('assets/close.png') }}) no-repeat center !important;
        }
        .lb-prev {
            background: url({{ asset('assets/prev.png') }}) no-repeat center !important;
            width: 34% !important;
        }
        .lb-next {
            background: url({{ asset('assets/next.png') }}) no-repeat center !important;
            width: 34% !important;
        }
        /* d */
    </style>
    <script>
        @if (!Auth::guard('user')->check())
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.add('light-mode');
            } else {
                document.documentElement.classList.remove('light-mode');
            }
        @endif
    </script>
</head>
<body class="{{ Auth::guard('user')->user() ? (App\Models\LightDarkMode::where('user_id', Auth::guard('user')->user()->id)->first()->value === 'light' ? 'light-mode' : '') : '' }}">

    <!-- Navbar Start -->
    <nav>
        <div id="search-modal">
            <div class="search-modal-content">
                <i id="closeModalBtn" class="fa-solid fa-square-xmark"></i>
                <div class="sm-search-box">
                    <input id="sm_search_input" type="search" value="{{ @$_GET['keyword'] }}" placeholder="What are you looking for?" />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
        </div>
        <i class="fa-solid fa-angle-right top2bottomICON"></i>

        <div class="container">
            <dev class="nav-main">
                <div class="logo item">
                    <a href="{{ route('index') }}">
                        @if (App\Models\Logo::exists())
                            <img src="{{ asset('upload/logo') }}/{{ App\Models\Logo::first()->logo }}" alt="" />
                        @else
                            <img src="{{ asset('assets/logo1.png') }}" alt="" />
                        @endif
                    </a>
                </div>
                <div class="icon responsive-mt-top item">
                    <a href='{{ route('index') }}'><i class="fa-solid fa-house"></i></a>
                    <a href='{{ route('category') }}'><i class="fa-solid fa-list"></i></a>
                    <a href="{{ route('video') }}"><i class="fa-solid fa-clapperboard"></i></a>
                    @if (Auth::guard('user')->user())
                        <a class="notification-box" href="{{ route('notification') }}"><i class="fa-solid fa-bell"><span>{{ App\Models\Notification::where('to_notification', Auth::guard('user')->user()->id)->where('see', 0)->count() }}</span></i></a>
                    @else
                        <a class="notification-box" href="{{ route('notification') }}"><i class="fa-solid fa-bell"></i></a>
                    @endif
                    <a href="{{ route('message') }}"><i class="fa-solid fa-comments"></i></a>
                    <i class="fa-solid fa-bars setting-icon"></i>
                </div>
                <div class="search item">
                    <input id="search_input" type="search" value="{{ @$_GET['keyword'] }}" placeholder="What are you looking for?" />
                    <i id="search_btn" class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="profile-language">
                    <i id="search-icon" class="fa-solid fa-magnifying-glass icon search-sm-icon"></i>
                    <div class='language-box-main'>
                        <i class="fa-solid fa-globe icon languageIcon"></i>
                        <div class="language-box">
                            <a href="{{ url('/lang/en') }}">
                                <button>
                                    English
                                    @if(app()->getLocale() == 'en')
                                        <i class="fa-solid fa-circle-check"></i>
                                    @endif
                                </button>
                            </a>
                            <a href="{{ url('/lang/bn') }}">
                                <button>
                                    বাংলা
                                    @if(app()->getLocale() == 'bn')
                                        <i class="fa-solid fa-circle-check"></i>
                                    @endif
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class='light-dark-box-main'>
                        <i class="fa-solid fa-sun icon lightDarkIcon"></i>
                        <div class="light-dark-box">
                            @if (Auth::guard('user')->user())
                                <button style="background: {{ App\Models\LightDarkMode::where('user_id', Auth::guard('user')->user()->id)->first()->value === 'light' ? 'var(--icon-bg-color)' : '' }}" onclick="toggleTheme('light')">Light</button>
                                <button style="background: {{ App\Models\LightDarkMode::where('user_id', Auth::guard('user')->user()->id)->first()->value === 'dark' ? 'var(--icon-bg-color)' : '' }}" onclick="toggleTheme('dark')">Dark</button>
                            @else
                                <button onclick="defaultToggleTheme('light')">Light</button>
                                <button onclick="defaultToggleTheme('dark')">Dark</button>
                            @endif
                        </div>
                    </div>
                    @if (Auth::guard('user')->user())
                        <a href="{{ route('profile') }}">
                            @if (Auth::guard('user')->user()->photo == null)
                                <img id="blah" class="user-profile" src="{{ Avatar::create(Auth::guard('user')->user()->fname.' '.Auth::guard('user')->user()->lname)->toBase64() }}" />
                            @else
                                @if (filter_var(Auth::guard('user')->user()->photo, FILTER_VALIDATE_URL))
                                    <img id="blah" class="user-profile" src="{{ Auth::guard('user')->user()->photo }}" alt="Google Profile Image" />
                                @else
                                    <img id="blah" class="user-profile" src="{{ asset('upload/users') }}/{{ Auth::guard('user')->user()->photo }}" alt="Local Profile Image" />
                                @endif
                            @endif
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success default-login-btn">Login</a>
                    @endif
                </div>
            </dev>
        </div>
    </nav>
    <!-- Navbar End -->

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    {{-- Layout start --}}
                    <div class="layout">
                        <div class="sm-light-dark-mode">
                            @if (Auth::guard('user')->user())
                                <button style="background: {{ App\Models\LightDarkMode::where('user_id', Auth::guard('user')->user()->id)->first()->value === 'light' ? 'var(--icon-bg-color)' : '' }}" onclick="toggleTheme('light')">Light</button>
                                <button style="background: {{ App\Models\LightDarkMode::where('user_id', Auth::guard('user')->user()->id)->first()->value === 'dark' ? 'var(--icon-bg-color)' : '' }}" onclick="toggleTheme('dark')">Dark</button>
                            @else
                                <button onclick="defaultToggleTheme('light')">Light</button>
                                <button onclick="defaultToggleTheme('dark')">Dark</button>
                            @endif
                        </div>
                        <ul>
                            <a href="{{ route('explor') }}">
                                <li>
                                    <img src="{{ asset('assets/explor.png') }}" alt="" />
                                    @lang('messages.explor')
                                </li>
                            </a>
                            <a href="{{ route('create.post') }}">
                                <li>
                                    <img src="{{ asset('assets/add.png') }}" alt="" />
                                    @lang('messages.createPost')
                                </li>
                            </a>
                            <a href="{{ route('qestion.answer') }}">
                                <li>
                                    <img src="{{ asset('assets/ask.png') }}" alt="" />
                                    @lang('messages.askQuestion')
                                </li>
                            </a>
                            <a href="{{ route('blog') }}">
                                <li>
                                    <img src="{{ asset('assets/pencil.png') }}" alt="" />
                                    @lang('messages.writeBlog')
                                </li>
                            </a>
                            <a href="{{ route('favourite') }}">
                                <li>
                                    <img src="{{ asset('assets/favourite.png') }}" alt="" />
                                    @lang('messages.favourite')
                                </li>
                            </a>
                            <a href="{{ route('friends') }}">
                                <li>
                                    <img src="{{ asset('assets/friend.png') }}" alt="" />
                                    @lang('messages.friends')
                                </li>
                            </a>
                            <a href="{{ route('setting') }}">
                                <li>
                                    <img src="{{ asset('assets/setting.png') }}" alt="" />
                                    @lang('messages.setting')
                                </li>
                            </a>
                            <a href="{{ route('support') }}">
                                <li>
                                    <img src="{{ asset('assets/customer-service.png') }}" alt="" />
                                    @lang('messages.support')
                                </li>
                            </a>
                            @if (Auth::guard('user')->user())
                                <a href="{{ route('user.logout') }}">
                                    <li>
                                        <img src="{{ asset('assets/logout.png') }}" alt="" />
                                        @lang('messages.logout')
                                    </li>
                                </a>
                            @endif
                        </ul>
                    </div>
                </div>



                <div class="col-lg-7 col-md-9 midddle-feild">
                    @yield('content')
                </div>



                <div class="col-lg-3 col-md-3">
                    <div class="right-sidebar">

                        @yield('right_sidebar')

                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/17.5.1/lazyload.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('js/lightbox.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.ably.com/lib/ably.min-1.js" type="text/javascript"></script>
    <script>
        const languageIcon = document.querySelector('.languageIcon')
        const languageBox = document.querySelector('.language-box')
        const lightDarkIcon = document.querySelector('.lightDarkIcon')
        const lightDarkBox = document.querySelector('.light-dark-box')

        languageIcon.addEventListener("click", () => {
            languageBox.classList.toggle('visible-languagebox')
            if (languageBox.classList.contains('visible-languagebox')) {
                lightDarkBox.classList.remove('visible-lightDark-box')
            }
        })

        lightDarkIcon.addEventListener("click", () => {
            lightDarkBox.classList.toggle('visible-lightDark-box')
            if (lightDarkBox.classList.contains('visible-lightDark-box')) {
                languageBox.classList.remove('visible-languagebox')
            }
        })

        $('.setting-icon').click(function() {
            if ($('nav').hasClass('top-to-bottom')) {
                $('.layout').css('top', '50px')
                $('.layout').toggleClass('visible-Layout-box')
            }else {
                if ($(window).width() > 768) {
                    $('.layout').css('top', '100px')
                    $('.layout').toggleClass('visible-Layout-box')
                }else {
                    $('.layout').css('top', '80px')
                    $('.layout').toggleClass('visible-Layout-box')
                }

            }
        })
        $(window).scroll(function() {
            $('.layout').removeClass('visible-Layout-box')
        })

        $('#search_btn').click(function() {
            let keyword = $('#search_input').val()
            const route = `{{ route('search') }}?keyword=${keyword}`
            window.location.href = route
        })
        $('.sm-search-box i').click(function() {
            let keyword = $('#sm_search_input').val()
            const route = `{{ route('search') }}?keyword=${keyword}`
            window.location.href = route
        })

        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $('nav').addClass('top-to-bottom');
            } else {
                $('nav').removeClass('top-to-bottom');
            }
        });

        $('.top2bottomICON').click(function() {
            $('html, body').animate({
                scrollTop: 0,
            }, 500);
        });
    </script>

    <script>
        function toggleTheme(value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/light/dark/mode',
                type: 'GET',
                data: {
                    value: value,
                },
                success: function(response) {
                    if(response == 'dark') {
                        $('body').removeClass('light-mode')
                    }else {
                        $('body').addClass('light-mode')
                    }
                }
            });
        }

        function defaultToggleTheme(theme) {
            localStorage.setItem('theme', theme);
            if(theme === 'light') {
                document.documentElement.classList.add('light-mode');
            }else {
                document.documentElement.classList.remove('light-mode');
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const swiper = new Swiper('.swiper-category', {
                loop: true,
                slidesPerView: 2,
                spaceBetween: 10,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    "@0.00": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@0.75": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@1.00": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@1.50": {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                },
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const swiper = new Swiper('.swiper', {
                loop: true,
                slidesPerView: 2,
                spaceBetween: 10,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    "@0.00": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@0.75": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@1.00": {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    "@1.50": {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                },
            });
        });
    </script>

    <script>
        const searchIcon = document.getElementById('search-icon');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('search-modal');
        searchIcon.addEventListener('click', () => {
            setTimeout(() => {
                modal.style.display = 'block';
            }, 10);
        });
        closeModalBtn.addEventListener('click', () => {
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        });
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModalBtn.click();
            }
        });
    </script>

    @yield('footer_script')

</body>
</html>
