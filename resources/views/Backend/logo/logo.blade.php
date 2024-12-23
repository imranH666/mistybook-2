@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Add Logo</h3>
                </div>
                <div class="card-body">
                    @if (session('added'))
                        <div class="alert alert-success">{{ session('added') }}</div>
                    @endif
                    @if (session('updated'))
                        <div class="alert alert-success">{{ session('updated') }}</div>
                    @endif
                    <form action="{{ route('add.logo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">Logo</label>
                            <br>
                            <label for="categoryIMG" class="form-label">
                                <img id="previewImg" class="category-image" src="{{ asset('assets/uplaod.png') }}" alt="">
                            </label>
                            <input onchange="document.getElementById('previewImg').src = window.URL.createObjectURL(this.files[0])" type="file" class="form-control" id="categoryIMG" name="logo" hidden>
                            @error('logo')
                                <br>
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Add Logo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
