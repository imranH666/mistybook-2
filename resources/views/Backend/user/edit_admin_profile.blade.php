@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Change Password</h3>
                </div>
                <div class="card-body">
                    @if (session('password_updated'))
                        <div class="alert alert-success">{{ session('password_updated') }}</div>
                    @endif
                    @if (session('current_pass_error'))
                        <div class="alert alert-warning">{{ session('current_pass_error') }}</div>
                    @endif
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="current_pass">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                            @error('current_password')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="password">New Password</label>
                            <input type="password" name="password" class="form-control">
                            @error('password')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                            @error('password_confirmation')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <button class="btn btn-primary" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
