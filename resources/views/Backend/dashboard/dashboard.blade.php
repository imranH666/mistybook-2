@extends('Backend.layout.admin')


@section('content')
    <h1 style="margin-top: 100px">Welcome to Dashboard</h1>
    <h5>{{ Auth::guard('admin')->user()->name }}</h5>
    <h5>{{ Auth::guard('admin')->user()->email }}</h5>
@endsection
