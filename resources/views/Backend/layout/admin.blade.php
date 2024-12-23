<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Mistybook</title>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css " rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('style/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('style/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend') }}/vendors/core/core.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="{{ asset('backend') }}/fonts/feather-font/css/iconfont.css">
	<link rel="stylesheet" href="{{ asset('backend') }}/vendors/flag-icon-css/css/flag-icon.min.css">
	<link rel="stylesheet" href="{{ asset('backend') }}/css/demo_1/style.css">
	<link rel="stylesheet" href="{{ asset('backend/css/common.css') }}">
</head>
<body>
	<div class="main-wrapper">
		<nav class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('assets/logo2.png') }}" alt="">
                </a>
                <div class="sidebar-toggler not-active">
                <span></span>
                <span></span>
                <span></span>
                </div>
            </div>
            <div class="sidebar-body">
            <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-category">Mistybook</li>
            <li class="nav-item">
                <a href="{{ route('language') }}" class="nav-link">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Language</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.category') }}" class="nav-link">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('add.chat.background') }}" class="nav-link">
                <i class="link-icon" data-feather="calendar"></i>
                <span class="link-title">Chat Background</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('upload.logo') }}" class="nav-link">
                <i class="link-icon" data-feather="calendar"></i>
                <span class="link-title">Logo</span>
                </a>
            </li>
            <li class="nav-item nav-category">User</li>
            <li class="nav-item">
                <a href="{{ route('user.list') }}" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">User List</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('see.user.reports') }}" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">User Post Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('see.user.support') }}" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">User Support</span>
                </a>
            </li>

            <li class="nav-item nav-category">Pages</li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#general-pages" role="button" aria-expanded="false" aria-controls="general-pages">
                <i class="link-icon" data-feather="book"></i>
                <span class="link-title">Special pages</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="general-pages">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                    <a href="pages/general/blank-page.html" class="nav-link">Blank page</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/general/faq.html" class="nav-link">Faq</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/general/invoice.html" class="nav-link">Invoice</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/general/profile.html" class="nav-link">Profile</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/general/pricing.html" class="nav-link">Pricing</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/general/timeline.html" class="nav-link">Timeline</a>
                    </li>
                </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#authPages" role="button" aria-expanded="false" aria-controls="authPages">
                <i class="link-icon" data-feather="unlock"></i>
                <span class="link-title">Authentication</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="authPages">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                    <a href="pages/auth/login.html" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                    <a href="pages/auth/register.html" class="nav-link">Register</a>
                    </li>
                </ul>
                </div>
            </li>

            </ul>
            </div>
        </nav>

        <nav class="settings-sidebar">
        <div class="sidebar-body">
            <a href="#" class="settings-sidebar-toggler">
            <i data-feather="settings"></i>
            </a>
            <h6 class="text-muted">Sidebar:</h6>
            <div class="form-group border-bottom">
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarLight" value="sidebar-light" checked>
                Light
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarDark" value="sidebar-dark">
                Dark
                </label>
            </div>
            </div>
            <div class="theme-wrapper">
            <h6 class="text-muted mb-2">Light Theme:</h6>
            <a class="theme-item active" href="../demo_1/dashboard-one.html">
                <img src="{{ asset('backend') }}/images/screenshots/light.jpg" alt="light theme">
            </a>
            <h6 class="text-muted mb-2">Dark Theme:</h6>
            <a class="theme-item" href="../demo_2/dashboard-one.html">
                <img src="{{ asset('backend') }}/images/screenshots/dark.jpg" alt="light theme">
            </a>
            </div>
        </div>
        </nav>
		<!-- partial -->

		<div class="page-wrapper">

			<!-- partial:partials/_navbar.html -->
			<nav class="navbar">
				<a href="#" class="sidebar-toggler">
					<i data-feather="menu"></i>
				</a>
				<div class="navbar-content">
					<form class="search-form">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<i data-feather="search"></i>
								</div>
							</div>
							<input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
						</div>
					</form>
					<ul class="navbar-nav">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="flag-icon flag-icon-us mt-1" title="us"></i> <span class="font-weight-medium ml-1 mr-1 d-none d-md-inline-block">English</span>
							</a>
							<div class="dropdown-menu" aria-labelledby="languageDropdown">
                                <a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-us" title="us" id="us"></i> <span class="ml-1"> English </span></a>
                                <a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-fr" title="fr" id="fr"></i> <span class="ml-1"> French </span></a>
                                <a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-de" title="de" id="de"></i> <span class="ml-1"> German </span></a>
                                <a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-pt" title="pt" id="pt"></i> <span class="ml-1"> Portuguese </span></a>
                                <a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-es" title="es" id="es"></i> <span class="ml-1"> Spanish </span></a>
							</div>
                        </li>

						<li class="nav-item dropdown nav-profile">
							<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								@if (Auth::guard('admin')->user()->photo == null)
                                    <img class="user-profile" src="{{ Avatar::create(Auth::guard('admin')->user()->name)->toBase64() }}" />
                                @else
                                    <img class="user-profile" src="{{ asset('upload/admins') }}/{{ Auth::guard('admin')->user()->photo }}" alt="" />
                                @endif
							</a>
							<div class="dropdown-menu" aria-labelledby="profileDropdown">
								<div class="dropdown-header d-flex flex-column align-items-center">
									<div class="figure mb-3">
										@if (Auth::guard('admin')->user()->photo == null)
                                            <img class="user-profile" src="{{ Avatar::create(Auth::guard('admin')->user()->name)->toBase64() }}" />
                                        @else
                                            <img class="user-profile" src="{{ asset('upload/admins') }}/{{ Auth::guard('admin')->user()->photo }}" alt="" />
                                        @endif
									</div>
									<div class="info text-center">
										<p class="name font-weight-bold mb-0">{{ Auth::guard('admin')->user()->name }}</p>
										<p class="email text-muted mb-3">{{{ Auth::guard('admin')->user()->email }}}</p>
									</div>
								</div>
								<div class="dropdown-body">
									<ul class="profile-nav p-0 pt-3">
										<li class="nav-item">
											<a href="{{ route('admin.profile.edit') }}" class="nav-link">
												<i data-feather="edit"></i>
												<span>Edit Profile</span>
											</a>
										</li>
										<li class="nav-item">
											<a href="{{ route('admin.logout') }}" class="nav-link">
												<i data-feather="log-out"></i>
												<span>Log Out</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<!-- partial -->

            <div class="page-content">
                @yield('content')
            </div>


			<footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between">
				<p class="text-muted text-center text-md-left">Copyright © 2024 <a href="#" target="_blank">Mistybook</a>. All rights reserved</p>
				<p class="text-muted text-center text-md-left mb-0 d-none d-md-block">Handcrafted With <i class="mb-1 text-primary ml-1 icon-small" data-feather="heart"></i></p>
			</footer>

		</div>
	</div>

    <script src="{{ asset('backend/js/all.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js "></script>
    <script src="{{ asset('backend') }}/vendors/core/core.js"></script>
    <script src="{{ asset('backend') }}/vendors/chartjs/Chart.min.js"></script>
    <script src="{{ asset('backend') }}/vendors/jquery.flot/jquery.flot.js"></script>
    <script src="{{ asset('backend') }}/vendors/jquery.flot/jquery.flot.resize.js"></script>
    <script src="{{ asset('backend') }}/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('backend') }}/vendors/apexcharts/apexcharts.min.js"></script>
    <script src="{{ asset('backend') }}/vendors/progressbar.js/progressbar.min.js"></script>
	<script src="{{ asset('backend') }}/vendors/feather-icons/feather.min.js"></script>
	<script src="{{ asset('backend') }}/js/template.js"></script>
    <script src="{{ asset('backend') }}/js/dashboard.js"></script>
    <script src="{{ asset('backend') }}/js/datepicker.js"></script>

    @yield('footer_script')

</body>
</html>
