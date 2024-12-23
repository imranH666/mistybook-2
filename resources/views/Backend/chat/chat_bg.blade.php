@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Add Chat Background</h3>
                </div>
                <div class="card-body">
                    @if (session('add_bg'))
                        <div class="alert alert-success">{{ session('add_bg') }}</div>
                    @endif
                    <form action="{{ route('add.chat.background.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label style="display: block" class="form-label">Add Chat Background</label>
                            <label for="chatBG">
                                <img id="previewImg" style="border: 1px solid gray; border-radius: 10px; cursor: pointer;" width="150px" height="100px" src="{{ asset('assets/uplaod.png') }}" alt="">
                            </label>
                            <input onchange="document.getElementById('previewImg').src = window.URL.createObjectURL(this.files[0])" id="chatBG" type="file" name="chat_bg" class="form-control" hidden>
                            <p></p>
                            @error('chat_bg')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Add Background</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3>Chat Background List</h3>
                </div>
                <div class="card-body">
                    @if (session('delete_bg'))
                        <div class="alert alert-success">{{ session('delete_bg') }}</div>
                    @endif
                    @if (session('change_status'))
                        <div class="alert alert-success">{{ session('change_status') }}</div>
                    @endif
                    <table class="table table-bordered">
                        <tr>
                            <th>SL</th>
                            <th>Chat Background</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                        @foreach ($chat_bgs as $sl=>$chat_bg)
                            <tr>
                                <td>{{ $sl + 1 }}</td>
                                <td>
                                    <img style="width:150px; height:100px; border-radius:5px" src="{{ asset('upload/chat-backgrounds') }}/{{ $chat_bg->chat_bg }}" alt="">
                                </td>
                                <td>
                                    <button onclick="window.location.href = '{{ route('update.status.chat.background', $chat_bg->id) }}'" class="badge {{ $chat_bg->status == 0? 'badge-success' : 'badge-warning' }}">{{ $chat_bg->status == 0? 'Activate' : 'Deactivate' }}</button>
                                </td>
                                <td><button onclick="window.location.href = '{{ route('delete.chat.background', $chat_bg->id) }}'" class="btn btn-danger">Delete</button></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
