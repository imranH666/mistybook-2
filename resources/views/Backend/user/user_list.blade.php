@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>User List</h3>
                </div>
                <div class="card-body">
                    <div class="user-list-container">
                        <table class="table table-bordered" style="white-space: normal">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th>Post</th>
                                <th>Blog</th>
                                <th>Question</th>
                                <th>Video</th>
                                <th>Joined</th>
                                <th>See</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($users as $sl => $user)
                                <tr>
                                    <td>{{ $sl + 1 }}</td>
                                    <td>{{ $user->fname.' '.$user->lname }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->photo == null)
                                            <img width="50" src="{{ Avatar::create( $user->fname.' '.$user->lname )->toBase64() }}" alt="">
                                        @else
                                            @if (filter_var($user->photo, FILTER_VALIDATE_URL))
                                                <img width="50" src="{{ $user->photo }}" alt="">
                                            @else
                                                <img width="50" src="{{ asset('upload/users') }}/{{ $user->photo }}" alt="">
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $user->rel_to_post_count->count() }}</td>
                                    <td>{{ $user->rel_to_blog_count->count() }}</td>
                                    <td>{{ $user->rel_to_question_count->count() }}</td>
                                    <td>{{ $user->rel_to_video_count->count() }}</td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                    <td><a href="{{ route('user.details', $user->id) }}" style="color:#9c06f3;"><i style="cursor: pointer; font-size:20px;" class="fa-solid fa-eye"></i></a></td>
                                    <td>
                                        <a class="badge badge-success" href="">Unban</a>
                                    </td>
                                    <td>
                                        <a class="btn btn-danger" href="">Delete</a>
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
