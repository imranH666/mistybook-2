@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="support-container card">
                <div class="card-header">
                    <h3>@lang('messages.support-team')</h3>
                    <p>@lang('messages.support-desc')</p>
                </div>
                <div class="card-body">
                    <form onsubmit="handleSubmit()" action="{{ route('support.store') }}" method="POST">
                        @csrf
                        <div class="create-post-content">
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
                                <input type="hidden" name="support_content" class="supportEditorContent">
                            </div>
                            <div class="post-editor-main">
                                <div contenteditable="true" class="post-editor support-editer"></div>
                            </div>
                            @if (Auth::guard('user')->user())
                                <button class="post-submit-btn" type="submit">@lang('messages.submit')</button>
                            @else
                                <a href="{{ route('login') }}">
                                    <button class="post-submit-btn" type="button">@lang('messages.submit')</button>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
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
        function handleSubmit() {
            const supportEditor = document.querySelector('.support-editer');
            const hiddenInput = document.querySelector('.supportEditorContent');

            const text = supportEditor.innerText.trim();
            if(text) {
                hiddenInput.value = supportEditor.innerHTML;
            }
        }

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

    <script>
         @if($errors->has('support_content'))
            Toastify({
                text: "{{ $errors->first('support_content') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "orange",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 7000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection
