@extends('Frontend.navber.navber')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('see.blog') }}">
                <button class="see-blog-list"><i class="fa-solid fa-eye"></i> @lang('messages.see-your-blog-list')</button>
            </a>
        </div>
        <div class="col-lg-12">
            <div class="blog-main">
                <form onsubmit="handleSubmit()" action="{{ route('create.blog') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="blog-input-feild">
                                <label>@lang('messages.blog-heading')</label>
                                <input type="text" value="{{ old('blog_heading') }}" name="blog_heading">
                                @error('blog_heading')
                                    <strong class="text text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-md-6">
                            <div class="blog-input-feild">
                                <label>@lang('messages.category')</label>
                                <select class="blog-categories" name="blog_category">
                                    <option class="blog-category-option" value="">@lang('messages.select')</option>
                                    @foreach ($categories as $category)
                                        <option class="blog-category-option" value="{{ $category->id }}">{{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}</option>
                                    @endforeach
                                </select>
                                @error('blog_category')
                                    <strong class="text text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-md-6">
                            <div class="blog-input-feild">
                                <label>@lang('messages.blog-banner-image')</label>
                                <label for="blogBanner">
                                    <img class="blogBanner" id="previewBologBannerImage" src="{{ asset('assets/uplaod.png') }}" alt="">
                                </label>
                                <input type="file" id="blogBanner" name="blog_banner" onchange="document.getElementById('previewBologBannerImage').src = window.URL.createObjectURL(this.files[0])" hidden>
                                @error('blog_banner')
                                    <strong class="text text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="blog-input-feild">
                                <div class="blog-editor-action-box">
                                    <button type="button" onclick="makeBold()">@lang('messages.bold')</button>
                                    <button type="button" onclick="makeItalic()">@lang('messages.italic')</button>
                                    <button type="button" onclick="makeUnderline()">@lang('messages.underline')</button>
                                    <button type="button"><label for="fontColor">@lang('messages.color')</label></button>
                                    <button type="button" onclick="setTextAlign('left')">@lang('messages.left')</button>
                                    <button type="button" onclick="setTextAlign('center')">@lang('messages.center')</button>
                                    <button type="button" onclick="setTextAlign('right')">@lang('messages.right')</button>
                                    <button type="button" onclick="setTextAlign('full')">@lang('messages.justify')</button>
                                    <button type="button" id="addImageBtn">@lang('messages.add-image')</button>
                                    <input type="file" class="imageInput" accept="image/*" hidden>
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
                                    <input type="hidden" name="blog_content" value="{{ old('blog_content') }}" class="postEditorContent">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="blog-editor-box">
                                <label class="mb-2">@lang('messages.blog-content')</label>
                                @error('blog_content')
                                    <strong class="text text-danger">{{ $message }}</strong>
                                @enderror
                                <div contenteditable="true" class="blog-editor">{!! old('blog_content') !!}</div>
                            </div>
                            @if (Auth::guard('user')->user())
                                <button class="blog-upload-btn" type="submit">@lang('messages.upload')</button>
                            @else
                                <a href="{{ route('login') }}">
                                    <button class="blog-upload-btn" type="button">@lang('messages.upload')</button>
                                </a>
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
        function handleSubmit() {
            const blogEditor = document.querySelector('.blog-editor');
            const hiddenInput = document.querySelector('.postEditorContent');

            const text = blogEditor.innerText.trim();
            if(text) {
                hiddenInput.value = blogEditor.innerHTML;
            }
        }

        const quill = new Quill('#editor-container', {
            theme: 'snow', // Other options: 'bubble'
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        // Text formatting
                    [{ 'header': 1 }, { 'header': 2 }],               // Headers
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],    // Lists
                    [{ 'color': [] }, { 'background': [] }],         // Text color & background
                    ['link', 'image'],                               // Links and images
                    ['clean']                                        // Remove formatting
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
        function setTextAlign(align) {
            document.execCommand('justify' + align, false, null);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addImageBtn = document.getElementById('addImageBtn');
            const imageInput = document.querySelector('.imageInput');
            const textEditor = document.querySelector('.blog-editor');

            if (!addImageBtn || !imageInput || !textEditor) {
                console.error('Element not found');
                return;
            }

            addImageBtn.addEventListener('click', () => {
                imageInput.value = "";
                imageInput.click();
            });

            imageInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '100%';
                        img.style.margin = '10px 0';
                        textEditor.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    <script>
        @if(session('upload_blog'))
            Toastify({
                text: "{{ session('upload_blog') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection
