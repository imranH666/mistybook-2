@extends('Backend.layout.admin')


@section('content')
    <div class="rwo">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>User Support List</h3>
                </div>
                <div class="card-body">
                    @if (session('deleted'))
                        <div class="alert alert-success">{{ session('deleted') }}</div>
                    @endif
                    <div class="support-container">
                        <table class="table table-bordered" style="white-space: normal">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th>Support</th>
                                <th>Time</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($supports as $sl => $support)
                                <tr>
                                    <td>{{ $sl + 1 }}</td>
                                    <td>{{ $support->rel_to_user->fname.' '.$support->rel_to_user->lname }}</td>
                                    <td>{{ $support->rel_to_user->email }}</td>
                                    <td>
                                        @if ($support->rel_to_user->photo == null)
                                            <img width="50" src="{{ Avatar::create( $support->rel_to_user->fname.' '.$support->rel_to_user->lname )->toBase64() }}" alt="">
                                        @else
                                            @if (filter_var($support->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                <img width="50" src="{{ $support->rel_to_user->photo }}" alt="">
                                            @else
                                                <img width="50" src="{{ asset('upload/users') }}/{{ $support->rel_to_user->photo }}" alt="">
                                            @endif
                                        @endif
                                    </td>
                                    <td>{!! $support->support_content !!}</td>
                                    <td>{{ $support->created_at }}</td>
                                    <td><a class="badge badge-primary" href="">Send Message</a></td>
                                    <td><a class="btn btn-danger" href="{{ route('user.support.delete', $support->id) }}">Delete</a></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
