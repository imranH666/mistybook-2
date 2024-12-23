@extends('Frontend.navber.navber')


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="create-post-main">
            <form onsubmit="handleSubmit()" action="{{ route('create.post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-3 col-sm-3">
                        <div class="create-post-img">
                            <label for="uploadPostImage">
                                <img src="{{ asset('assets/uplaod.png') }}" alt="">
                            </label>
                            <input type="file" name="post_image[]" id="uploadPostImage" onchange="previewImages()" accept="image/*" multiple hidden>
                        </div>
                        <div id="imagePreviewContainer"></div>
                    </div>
                    <div class="col-lg-9 col-sm-9">
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
                                <input type="hidden" name="post_content" class="postEditorContent">
                            </div>
                            <div class="post-editor-main">
                                <div contenteditable="true" class="post-editor"></div>
                            </div>
                            {{-- <textarea name="post_content" placeholder="Write a post....."></textarea> --}}
                            @if (Auth::guard('user')->user())
                                <button class="post-submit-btn" type="submit">@lang('messages.upload')</button>
                            @else
                                <a href="{{ route('login') }}">
                                    <button class="post-submit-btn" type="button">@lang('messages.upload')</button>
                                </a>
                            @endif
                        </div>
                        @if (Auth::guard('user')->user())
                            <div class="post-list">
                                @foreach ($posts as $post)
                                    <div class="post-list-item">
                                        <i style="cursor: pointer" onclick="deletePost('{{ route('post.delete', $post->id) }}')" class="fa-solid fa-trash post-delete-btn"></i>
                                        <a href="{{ route('show.post', $post->slug) }}">
                                            <p>{!! Str::limit(strip_tags($post->content), 150, '...') !!}</p>
                                            <div class="row g-0">
                                                @php
                                                    $imageCount = $post->rel_to_post_images->count();
                                                @endphp
                                                @foreach ($post->rel_to_post_images as $img)
                                                    <div class="col-lg-{{ $imageCount == 1 ? '12' : '6' }}">
                                                        <img src="{{ asset('upload/posts') }}/{{ $img->image_path }}" alt="">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="share-status">{{ $post->share == 0? '' : 'Shared' }}</div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')
    @include('Frontend.layout.right_side_blogs')
@endsection


@section('footer_script')
    <script>
        function deletePost(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#579406",
                cancelButtonColor: "#ff00d4",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "The Post deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }

        function previewImages() {
            let previewContainer = document.getElementById('imagePreviewContainer');
            previewContainer.innerHTML = "";

            let files = document.getElementById('uploadPostImage').files;

            for (let i = 0; i < files.length; i++) {
                let file = files[i];

                if (file.type.startsWith("image/")) {
                    let imgElement = document.createElement("img");
                    imgElement.className = "previewimages"
                    imgElement.src = URL.createObjectURL(file);
                    imgElement.style.border = "2px solid #ddd";
                    imgElement.style.padding = "5px";
                    previewContainer.appendChild(imgElement);
                }
            }
        }
    </script>

    <script>
        function handleSubmit() {
            const postEditor = document.querySelector('.post-editor');
            const hiddenInput = document.querySelector('.postEditorContent');

            const text = postEditor.innerText.trim();
            if(text) {
                hiddenInput.value = postEditor.innerHTML;
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
        @if($errors->has('post_content'))
            Toastify({
                text: "{{ $errors->first('post_content') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "purple",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('post_image'))
            Toastify({
                text: "{{ $errors->first('post_image') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "purple",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('post_image.*'))
            Toastify({
                text: "{{ $errors->first('post_image.*') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "purple",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('post_uploaded'))
            Toastify({
                text: "{{ session('post_uploaded') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('post_delete'))
            Toastify({
                text: "{{ session('post_delete') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection


