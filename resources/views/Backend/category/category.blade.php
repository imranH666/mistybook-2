@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Add Category</h3>
                </div>
                <div class="card-body">
                    @if (session('add_category'))
                        <div class="alert alert-success">{{ session('add_category') }}</div>
                    @endif
                    <form action="{{ route('add.category') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="language-div">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Category Bangla Name</label>
                                        <input type="text" class="form-control" name="category_bangla_name" value="{{ old('category_bangla_name') }}">
                                        @error('category_bangla_name')
                                            <strong class="text text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Category English Name</label>
                                        <input type="text" class="form-control" name="category_english_name" value="{{ old('category_english_name') }}">
                                        @error('category_english_name')
                                            <strong class="text text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category Bangla Description</label>
                            <textarea class="form-control" name="category_bangla_disc">{{ old('category_bangla_disc') }}</textarea>
                            @error('category_bangla_disc')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category English Description</label>
                            <textarea class="form-control" name="category_english_disc">{{ old('category_english_disc') }}</textarea>
                            @error('category_english_disc')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Category Image</label>
                            <br>
                            <label for="categoryIMG" class="form-label">
                                <img id="previewImg" class="category-image" src="{{ asset('assets/uplaod.png') }}" alt="">
                            </label>
                            <input onchange="document.getElementById('previewImg').src = window.URL.createObjectURL(this.files[0])" type="file" class="form-control" id="categoryIMG" name="category_image" hidden>
                            @error('category_image')
                                <br>
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3>Category List</h3>
                </div>
                <div class="card-body">
                    @if (session('not_found'))
                        <div class="alert alert-warning">{{ session('not_found') }}</div>
                    @endif
                    <div style="overflow-x: auto;">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL</th>
                                <th>Category Bangla Name</th>
                                <th>Category English Name</th>
                                <th>Category Bangla Description</th>
                                <th>Category English Description</th>
                                <th>Category Image</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($categories as $sl=>$category)
                                <tr>
                                    <td>{{ $sl + 1 }}</td>
                                    <td>{{ $category->category_bangla_name }}</td>
                                    <td>{{ $category->category_english_name }}</td>
                                    <td style="white-space: normal">{{ $category->category_bangla_description }}</td>
                                    <td style="white-space: normal">{{ $category->category_english_description }}</td>
                                    <td>
                                        <img class="category-image" src="{{ asset('upload/categories') }}/{{ $category->category_image }}" alt="">
                                    </td>
                                    <td>
                                        <button onclick="deleteCategory('{{ route('delete.category', $category->id) }}')" style="border: none" class="badge badge-danger">Delete</button>
                                        <a href="{{ route('edit.category', $category->id) }}" class="badge badge-primary">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_script')
    <script>
        function deleteCategory(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "green",
                cancelButtonColor: "red",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                        window.location.href = url
                        Swal.fire({
                        title: "Deleted!",
                        text: "Category deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }
    </script>
@endsection
