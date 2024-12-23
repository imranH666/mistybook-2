@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>User Post Reports</h3>
                </div>
                <div class="card-body">
                    @if (session('deleted'))
                        <div class="alert alert-success">{{ session('deleted') }}</div>
                    @endif
                    <div class="report-container">
                        <table class="table table-bordered" style="white-space: normal">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th>Reports</th>
                                <th>Time</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>

                            @foreach ($reports as $sl => $report)
                                <tr>
                                    <td>{{ $sl + 1 }}</td>
                                    <td>{{ $report->rel_to_user->fname.' '.$report->rel_to_user->lname }}</td>
                                    <td>{{ $report->rel_to_user->email }}</td>
                                    <td>
                                        @if ($report->rel_to_user->photo == null)
                                            <img width="50" src="{{ Avatar::create( $report->rel_to_user->fname.' '.$report->rel_to_user->lname )->toBase64() }}" alt="">
                                        @else
                                            @if (filter_var($report->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                <img width="50" src="{{ $report->rel_to_user->photo }}" alt="">
                                            @else
                                                <img width="50" src="{{ asset('upload/users') }}/{{ $report->rel_to_user->photo }}" alt="">
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $report->report_text }}</td>
                                    <td>{{ $report->created_at }}</td>
                                    <td><a class="badge badge-primary" href="{{ route('user.reports.message', $report->rel_to_user->id) }}">Send Message</a></td>
                                    <td><a class="btn btn-danger" href="{{ route('user.reports.delete', $report->id) }}">Delete</a></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
