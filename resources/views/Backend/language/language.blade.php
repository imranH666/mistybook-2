@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Add Language</h3>
                </div>
                <div class="card-body">
                    @if (session('added'))
                        <div class="alert alert-success">{{ session('added') }}</div>
                    @endif
                    <form action="{{ route('add.language') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label">Language Name</label>
                            <input type="text" class="form-control" name="language_name">
                            @error('language_name')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Add Language</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3>Language List</h3>
                </div>
                <div class="card-body">
                    @if (session('deleted'))
                        <div class="alert alert-success">{{ session('deleted') }}</div>
                    @endif
                    <table class="table table-bordered">
                        <tr>
                            <th>SL</th>
                            <th>Language Name</th>
                            <th>Action</th>
                        </tr>
                        @foreach ($languages as $sl=>$language)
                            <tr>
                                <td>{{ $sl + 1 }}</td>
                                <td>{{ $language->language }}</td>
                                <td>
                                    <a class="badge badge-danger" href="{{ route('delete.language', $language->id) }}">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
