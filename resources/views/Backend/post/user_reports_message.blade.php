@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3>Send Message</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.reports.message.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" for="report">Message</label>
                            <textarea class="form-control" name="report_text" style="height: 150px" id="ace_html" class="ace-editor w-100"></textarea>
                            @error('report_text')
                                <strong class="text text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
