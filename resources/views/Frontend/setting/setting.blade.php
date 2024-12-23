@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <div class="update-password-container">
                <div class="card">
                    <div class="card-header">
                        <h4>Update Name</h4>
                    </div>
                    @php
                        $user = Auth::guard('user')->user();
                    @endphp
                    <div class="card-body">
                        <form action="{{ route('update.name') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="fname">First Name</label>
                                        <input type="text" value="{{ $user->fname }}" name="fname" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="lname">Last Name</label>
                                        <input type="text" value="{{ $user->lname }}" name="lname" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <button type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('change.password') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label for="current_pass">Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label for="password">New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="mb-2">
                                <button type="submit">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6">
            <div class="update-password-container">
                <div class="card">
                    <div class="card-header">
                        <h4>Update Category</h4>
                    </div>
                    @php
                        $user = Auth::guard('user')->user();
                    @endphp
                    <div class="card-body">
                        <p class="setting-category-desc">@lang('messages.read-categories-interested')</p>
                        <form action="{{ route('update.user.category') }}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach ($categories as $category)
                                    <div class="col-lg-6">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="categories[]"
                                            id="category-{{ $category->id }}"
                                            value="{{ $category->id }}"
                                            autocomplete="off"
                                            {{ in_array($category->id, $userCategories) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                            {{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-2 mt-3">
                                <button type="submit">Update</button>
                            </div>
                        </form>
                    </div>
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
        @if($errors->has('fname'))
            Toastify({
                text: "{{ $errors->first('fname') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('current_password'))
            Toastify({
                text: "{{ $errors->first('current_password') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('password'))
            Toastify({
                text: "{{ $errors->first('password') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('password_confirmation'))
            Toastify({
                text: "{{ $errors->first('password_confirmation') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif

        @if(session('name_updated'))
            Toastify({
                text: "{{ session('name_updated') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('password_updated'))
            Toastify({
                text: "{{ session('password_updated') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('current_pass_error'))
            Toastify({
                text: "{{ session('current_pass_error') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif

        @if(session('updated_category'))
            Toastify({
                text: "{{ session('updated_category') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('no_user'))
            Toastify({
                text: "{{ session('no_user') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection
