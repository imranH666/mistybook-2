@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="video-input-feild">
                <label class="label" for="">Title</label>
                <div class="post-editor-main video">
                    <div class="post-editor-action-box">
                        <button type="button" onclick="makeBold()">@lang('messages.bold')</button>
                        <button type="button" onclick="makeItalic()">@lang('messages.italic')</button>
                        <button type="button" onclick="makeUnderline()">@lang('messages.underline')</button>
                        <button type="button"><label for="fontColor">@lang('messages.color')</label></button>
                        <select id="fontSize" onchange="changeFontSize()">
                            <option value="">@lang('messages.font-size')</option>
                            <option value="1">@lang('messages.10px')</option>
                            <option value="2">@lang('messages.13px')</option>
                            <option value="3">@lang('messages.16px')</option>
                            <option value="4">@lang('messages.18px')</option>
                            <option value="5">@lang('messages.24px')</option>
                            <option value="6">@lang('messages.32px')</option>
                            <option value="7">@lang('messages.48px')</option>
                        </select>
                        <input type="color" id="fontColor" onchange="changeFontColor()" title="Choose Font Color" hidden>
                        <input type="hidden" name="video_content" class="videoEditorContent">
                    </div>
                    <div contenteditable="true" class="video-editor"></div>
                </div>
                @if (Auth::guard('user')->user())
                    <button onclick="videoStore()" type="button" class="video-submit-btn">Submit</button>
                @else
                    <button onclick="window.location.href = '{{ route('login') }}'" type="button" class="video-submit-btn">Submit</button>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="video-input-feild sm-video-input-feild">
                <label class="label" style="display: block" for="videoFile">Select Video</label>
                @if (Auth::guard('user')->user())
                    <label for="browseFile">
                        <img src="{{ asset('assets/youtube.png') }}" alt="">
                    </label>
                @else
                    <a href="{{ route('login') }}"><img src="{{ asset('assets/youtube.png') }}" alt=""></a>
                @endif
                <input type="file" id="browseFile" name="video" class="form-control" accept="video/*" hidden>
            </div>
            <div class="progress my-3" style="display: none;">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="preview-video-container" style="display: none;">
                <video id="videoPreview" controls width="100%" src=""></video>
            </div>
        </div>
    </div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')
    @include('Frontend.layout.recent_blog_slidebar')
@endsection


@section('footer_script')
    <script>
        let store_video_name = null

        let browseFile = $('#browseFile');
        var resumable = new Resumable({
            target: '{{ route('upload.video') }}',
            query: { _token: '{{ csrf_token() }}' },
            fileType: ['mp4', 'mov', 'avi', 'wmv'],
            headers: {
                'Accept': 'application/json'
            },
            testChunks: false,
            throttleProgressCallbacks: 1,
        });
        resumable.assignBrowse(browseFile[0]);

        resumable.on('fileAdded', function (file) {
            const maxSizeInBytes = 200 * 1024 * 1024; // 200 MB
            if (file.size > maxSizeInBytes) {
                Toastify({
                    text: "The video file size exceeds 200 MB. Please choose a smaller file.",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "purple",
                    stopOnFocus: true,
                }).showToast();
                resumable.removeFile(file);
                return;
            }
            showProgress();
            resumable.upload();
        });

        resumable.on('fileProgress', function (file) {
            let progressValue = Math.floor(file.progress() * 100);
            if(file.progress() > 0.99) {
                $('.progress-bar').html('Compressing...');
                $('.progress-bar').css('background', `linear-gradient(45deg, #f788f1, #9fa8fd)`);
                if(file.progress() == 1) {
                    updateProgress(progressValue);
                }
            }else {
                updateProgress(progressValue);
            }
        });

        resumable.on('fileSuccess', function (file, response) {
            response = JSON.parse(response);
            store_video_name = response.filename
            $('#videoPreview').attr('src', "{{ asset('upload/videos') }}/"+response.filename);
            $('.preview-video-container').show();
        });

        resumable.on('fileError', function (file, response) {
            console.log('Upload Error testing:', response);
        });

        let progress = $('.progress');
        function showProgress() {
            progress.find('.progress-bar').css('width', '0%');
            progress.find('.progress-bar').html('0%');
            progress.find('.progress-bar').removeClass('bg-success');
            progress.show();
        }

        function updateProgress(value) {
            progress.find('.progress-bar').css('width', `${value}%`);
            progress.find('.progress-bar').html(`${value}%`);
            if (value === 100) {
                progress.find('.progress-bar').addClass('bg-success');
            }
        }

        function videoStore() {
            let video_content = null
            const videoEditor = document.querySelector('.video-editor');

            const text = videoEditor.innerText.trim();
            if(text) {
                video_content = videoEditor.innerHTML;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/store/video',
                type: 'POST',
                data: {
                    video_content: video_content,
                    video_name: store_video_name,
                },
                success: function(response) {
                    if(response == 'error') {
                        Toastify({
                            text: "Please, write somthing!",
                            duration: 5000,
                            gravity: "top",
                            position: "center",
                            backgroundColor: "#fdbb05",
                            stopOnFocus: true,
                        }).showToast();
                    }
                    if(response == 'video_error') {
                        Toastify({
                            text: "Please, select a video!",
                            duration: 5000,
                            gravity: "top",
                            position: "center",
                            backgroundColor: "purple",
                            stopOnFocus: true,
                        }).showToast();
                    }
                    if(response == 'success') {
                        store_video_name = null
                        videoEditor.innerHTML = ''
                        $('.preview-video-container').hide();
                        progress.hide();
                        Toastify({
                            text: "Video uploaded successfully!",
                            duration: 5000,
                            gravity: "top",
                            position: "center",
                            backgroundColor: "green",
                            stopOnFocus: true,
                        }).showToast();
                    }
                }
            });
        }
    </script>

    <script>
        const quill = new Quill('#editor-container', {
            theme: 'snow', // Other options: 'bubble'
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        function makeBold() {
            document.execCommand('bold');
        }

        function makeItalic() {
            document.execCommand('italic');
        }

        function makeUnderline() {
            document.execCommand('underline');
        }

        function changeFontSize() {
            const fontSize = document.getElementById('fontSize').value;
            if (fontSize) {
                document.execCommand('fontSize', false, fontSize);
            }
        }

        function changeFontColor() {
            const fontColor = document.getElementById('fontColor').value;
            if (fontColor) {
                document.execCommand('foreColor', false, fontColor);
            }
        }
    </script>
@endsection
