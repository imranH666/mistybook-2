@extends('Backend.layout.admin')

@section('content')
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Category</h3>
                </div>
                <div class="card-body">
                    @if (session('update_category'))
                        <div class="alert alert-success">{{ session('update_category') }}</div>
                    @endif
                    <form action="{{ route('update.edit.category', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="language-div">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Category Bangla Name</label>
                                        <input type="text" class="form-control" name="category_bangla_name" value="{{ $category->category_bangla_name }}">
                                        @error('category_bangla_name')
                                            <strong class="text text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Category English Name</label>
                                        <input type="text" class="form-control" name="category_english_name" value="{{ $category->category_english_name }}">
                                        @error('category_english_name')
                                            <strong class="text text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category Bangla Description</label>
                            <textarea class="form-control" name="category_bangla_disc">{{ $category->category_bangla_description }}</textarea>
                            @error('category_bangla_disc')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category English Description</label>
                            <textarea class="form-control" name="category_english_disc">{{ $category->category_english_description }}</textarea>
                            @error('category_english_disc')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category Image</label>
                            <br>
                            <label for="categoryIMG" class="form-label">
                                <img id="previewImg" class="category-image" src="{{ asset('upload/categories') }}/{{ $category->category_image }}" alt="">
                            </label>
                            <input onchange="document.getElementById('previewImg').src = window.URL.createObjectURL(this.files[0])" type="file" class="form-control" id="categoryIMG" name="category_image" hidden>
                            @error('category_image')
                                <br>
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
