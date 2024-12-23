@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="favourite-head">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">@lang('messages.post')</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">@lang('messages.question-text')</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">@lang('messages.blog')</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-video-tab" data-bs-toggle="pill" data-bs-target="#pills-video" type="button" role="tab" aria-controls="pills-video" aria-selected="false">@lang('messages.video')</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="fevourite-container">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                        <div class="post-favourite-main-container">
                            <div class="row">
                                @forelse ($posts as $post)
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="post-favourite-item">
                                            <i onclick="deleteFavouritePost('{{ route('delete.favourite.post', $post->rel_to_post->id) }}')" class="fa-solid fa-circle-xmark post-favourite-delete"></i>
                                            <a href="{{ route('show.post', $post->rel_to_post->slug) }}">
                                                <div class="row g-0">
                                                    @php
                                                        $imageCount = $post->rel_to_post->rel_to_post_images->count();
                                                    @endphp
                                                    @foreach ($post->rel_to_post->rel_to_post_images as $image)
                                                        <div class="col-lg-{{ $imageCount == 1 ? '12' : '6' }} col-{{ $imageCount == 1 ? '12' : '6' }} col-sm-{{ $imageCount == 1 ? '12' : '6' }} col-md-{{ $imageCount == 1 ? '12' : '6' }}">
                                                            <img style="height: {{ $imageCount <= 2 ? '170px' : '85px' }}" src="{{ asset('upload/posts') }}/{{ $image->image_path }}" alt="">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <p>{{ Str::limit(strip_tags($post->rel_to_post->content), 200) }}</p>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="favourite-question-empty">
                                        <img src="{{ asset('assets/box.png') }}" alt="">
                                        <h6>Empty</h6>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade favourite-question-list-container" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                        @forelse ($questions as $question)
                            <div class="question-viewed fevourite-question-viewed question-viewed{{ $question->rel_to_question->id }}">
                                <i onclick="deleteQuestion('{{ route('delete.favourite.question', $question->rel_to_question->id) }}')" class="fa-solid fa-rectangle-xmark hide-question-btn"></i>
                                <h6>{{ $question->rel_to_question->question }}</h6>
                                <p class="question-viewed-btn" onclick="open_answer_list({{ $question->rel_to_question->id }})">@lang('messages.answers') ({{ $question->rel_to_question->rel_to_answer->count() }})</p>
                                <div class="question-action">
                                    <button class="answer-btn" onclick="open_answer_box({{ $question->rel_to_question->id }})"><i class="fa-solid fa-pen-to-square"></i> @lang('messages.answer')</button>
                                    @if ( Auth::guard('user')->user())
                                        @php
                                            $isFollowing = App\Models\Following_Follower::where('follower', $question->rel_to_question->user_id)
                                                ->where('following',  Auth::guard('user')->user()->id)
                                                ->exists();
                                        @endphp
                                    @endif
                                    @if (Auth::guard('user')->user())
                                        <button style="color: {{ $isFollowing ? '#f59608' : '' }}" class="commonFollowFollowingStatus{{ $question->rel_to_question->user_id }}" onclick="followingFunc({{ $question->rel_to_question->user_id }})">
                                            @if ($isFollowing)
                                                <i class="fa-solid fa-user-plus"></i>
                                                @lang('messages.following')
                                            @else
                                                <i class="fa-solid fa-user-plus"></i>
                                                @lang('messages.follow')
                                            @endif
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <button>
                                                <i class="fa-solid fa-user-plus"></i>
                                                @lang('messages.follow')
                                            </button>
                                        </a>
                                    @endif
                                    <button onclick="copyLinkFunc({{ $question->rel_to_question->id }})"><i class="fa-regular fa-copy"></i> @lang('messages.link')</button>
                                    <input type="text" class="copyInput{{ $question->rel_to_question->id }}" value="{{ config('app.url') }}/question/{{ $question->rel_to_question->slug }}" hidden>
                                </div>
                                <div class="answer-input-box answer-input-box{{ $question->rel_to_question->id }}">
                                    <form id="answerEditorForm{{ $question->rel_to_question->id }}" onsubmit="handleSubmit({{ $question->rel_to_question->id }})" action="{{ route('answer.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="text-editor-main">
                                            <div contenteditable="true" id="text-editor" class="text-editor{{ $question->rel_to_question->id }}"></div>
                                            <div class="text-editor-action-box">
                                                <button type="button" onclick="makeBold()">Bold</button>
                                                <button type="button" onclick="makeItalic()">Italic</button>
                                                <button type="button" id="addImageBtn{{ $question->rel_to_question->id }}" onclick="addImageFunc({{ $question->rel_to_question->id }})">Add Image</button>
                                                <input type="file" class="imageInput{{ $question->rel_to_question->id }}" accept="image/*" hidden>
                                                <button type="button" onclick="makeUnderline()">Underline</button>
                                                <select id="fontSize{{ $question->rel_to_question->id }}" onchange="changeFontSize({{ $question->rel_to_question->id }})">
                                                    <option value="">Font Size</option>
                                                    <option value="1">10px</option>
                                                    <option value="2">13px</option>
                                                    <option value="3">16px</option>
                                                    <option value="4">18px</option>
                                                    <option value="5">24px</option>
                                                    <option value="6">32px</option>
                                                    <option value="7">48px</option>
                                                </select>
                                                <input type="color" id="fontColor{{ $question->rel_to_question->id }}" onchange="changeFontColor({{ $question->rel_to_question->id }})" title="Choose Font Color">
                                                <input type="hidden" name="answer" id="answerEditorContent{{ $question->rel_to_question->id }}">
                                                <input type="hidden" name="question_id" value="{{ $question->rel_to_question->id }}">
                                            </div>
                                        </div>
                                        <button class="answer-input-btn" type="submit">@lang('messages.answer')</button>
                                    </form>
                                </div>
                                <div class="answers-container answers-container{{ $question->rel_to_question->id }}">
                                    @forelse (App\Models\Answer::where('question_id', $question->rel_to_question->id)->latest()->get() as $answer)
                                        <div class="answers-item">
                                            <div class="answer-user-profile">
                                                @if ($answer->rel_to_user->photo == null)
                                                    <img class="answer-user-profile-img" src="{{ Avatar::create($answer->rel_to_user->fname.' '.$answer->rel_to_user->lname)->toBase64() }}" />
                                                @else
                                                    @if (filter_var($answer->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                        <img class="answer-user-profile-img" src="{{ $answer->rel_to_user->photo }}" alt="" />
                                                    @else
                                                        <img class="answer-user-profile-img" src="{{ asset('upload/users') }}/{{ $answer->rel_to_user->photo }}" alt="" />
                                                    @endif
                                                @endif
                                                <div class="answer-user-name">
                                                    <h5>{{ $answer->rel_to_user->fname }} {{ $answer->rel_to_user->lname }}</h5>
                                                    <h6>{{ $answer->created_at->diffForHumans() }}</h6>
                                                </div>
                                            </div>
                                            <div class="answer-content">
                                                {!! $answer->answer !!}
                                            </div>
                                        </div>
                                    @empty
                                        <p>Not yet answered</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="favourite-question-empty">
                                <img src="{{ asset('assets/box.png') }}" alt="">
                                <h6>Empty</h6>
                            </div>
                        @endforelse
                    </div>
                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                        <div class="blog-favourite-container">
                            <div class="row">
                                @forelse ($blogs as $blog)
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="blog-favourite-item">
                                            <i onclick="deleteFavouriteBlog('{{ route('delete.favourite.blog', $blog->rel_to_blog->id) }}')" class="fa-solid fa-xmark"></i>
                                            <a href="{{ route('read.blog', $blog->rel_to_blog->slug) }}">
                                                <img src="{{ asset('upload/blogs') }}/{{ $blog->rel_to_blog->blog_banner }}" alt="">
                                                <h5>{{ Str::limit($blog->rel_to_blog->blog_title, 80, '...') }}</h5>
                                                <p>{!! Str::limit(strip_tags($blog->rel_to_blog->blog_content), 200) !!}</p>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="favourite-question-empty">
                                        <img src="{{ asset('assets/box.png') }}" alt="">
                                        <h6>Empty</h6>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab" tabindex="0">
                        <div class="blog-favourite-container">
                            <div class="row">
                                @forelse ($videos as $video)
                                    <div class="col-lg-12">
                                        <div class="video-container">
                                            <div class="video-item video-favourite-item">
                                                <i onclick="deleteFavouriteVideo('{{ route('delete.favourite.video', $video->rel_to_video->id) }}')" class="fa-solid fa-trash my-video-delete-btn"></i>
                                                <div class="video-user-profile">
                                                    <a href="{{ route('user.profile', $video->rel_to_video->rel_to_user->slug) }}">
                                                        @if ($video->rel_to_video->rel_to_user->photo == null)
                                                            <img class="user-profile" src="{{ Avatar::create($video->rel_to_video->rel_to_user->fname.' '.$video->rel_to_video->rel_to_user->lname)->toBase64() }}" />
                                                        @else
                                                            @if (filter_var($video->rel_to_video->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                <img src="{{ $video->rel_to_video->rel_to_user->photo }}" alt="">
                                                            @else
                                                                <img src="{{ asset('upload/users') }}/{{ $video->rel_to_video->rel_to_user->photo }}" alt="">
                                                            @endif
                                                        @endif
                                                    </a>
                                                    <div class="video-user-name">
                                                        <h5><a href="{{ route('user.profile', $video->rel_to_video->rel_to_user->slug) }}">{{ $video->rel_to_video->rel_to_user->fname }} {{ $video->rel_to_video->rel_to_user->lname }}</a>
                                                            <span onclick="followingFunc({{ $video->rel_to_video->rel_to_user->id }})" class="commonFollowFollowingStatus{{ $video->rel_to_video->rel_to_user->id }}">
                                                                @if (App\Models\Following_Follower::where('follower', $video->rel_to_video->rel_to_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                                                    @lang('messages.following')
                                                                @else
                                                                    @lang('messages.follow')
                                                                @endif
                                                            </span>
                                                        </h5>
                                                        <h6>{{ $video->created_at->diffForHumans() }}</h6>
                                                    </div>
                                                </div>
                                                <div class="video-content">{!! $video->video_content !!}</div>
                                                <div class="video-div">
                                                    <div class="play-next-prev-btns">
                                                        <i onclick="backwardButton({{ $video->rel_to_video->id }})" class="fa-solid fa-arrow-rotate-left"><span>10</span></i>
                                                        <i onclick="videoPlayBtn({{ $video->rel_to_video->id }})" class="fa-solid fa-play play-btn play{{  $video->rel_to_video->id }}"></i>
                                                        <i onclick="forwardButton({{ $video->rel_to_video->id }})" class="fa-solid fa-arrow-rotate-right"><span>10</span></i>
                                                    </div>
                                                    <div class="video-range-time">
                                                        <div class="time-and-time">
                                                            <span id="currentTime{{ $video->rel_to_video->id }}">0:00</span>
                                                            <span id="totalDuration{{ $video->rel_to_video->id }}">0:00</span>
                                                        </div>
                                                        <input type="range" id="progress{{ $video->rel_to_video->id }}" value="0" max="100">
                                                    </div>
                                                    <video id="video{{ $video->rel_to_video->id }}" src="{{ asset('upload/videos') }}/{{ $video->rel_to_video->video_name }}"></video>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="favourite-question-empty">
                                        <img src="{{ asset('assets/box.png') }}" alt="">
                                        <h6>Empty</h6>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
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
        function open_my_question_answer(question_id) {
            $('.my-answer-list-container'+question_id).fadeToggle(500, 'swing')
        }

        function open_answer_box(question_id) {
            $('.answer-input-box'+question_id).fadeToggle(500, 'swing');
        }

        function open_answer_list(question_id) {
            $('.answers-container'+question_id).fadeToggle(500, 'swing');
        }

        function handleSubmit(id) {
            const textEditor2 = document.querySelector('.text-editor'+id);
            const hiddenInput = document.getElementById('answerEditorContent'+id);

            hiddenInput.value = textEditor2.innerHTML;
        }
    </script>

    <script>
        @if($errors->has('question'))
            Toastify({
                text: "{{ $errors->first('question') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "pink",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if($errors->has('answer'))
            Toastify({
                text: "{{ $errors->first('answer') }}",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "pink",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('store_question'))
            Toastify({
                text: "{{ session('store_question') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
        @if(session('store_answer'))
            Toastify({
                text: "{{ session('store_answer') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>

    <script>
        const quill = new Quill('#editor-container', {
            theme: 'snow',
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

        function addImage() {
            const url = prompt('Enter image URL:');
            if (url) {
                document.execCommand('insertImage', false, url);
            }
        }
    </script>

    <script>
        function addImageFunc(id) {
            const addImageBtn = document.getElementById('addImageBtn' + id);
            const imageInput = document.querySelector('.imageInput' + id);

            if (!addImageBtn || !imageInput) {
                console.error(`Add Image button or input not found for question ID ${id}`);
                return;
            }

            // Prevent multiple event listeners
            if (!addImageBtn.hasAttribute('data-listener')) {
                addImageBtn.addEventListener('click', () => {
                    imageInput.click(); // Trigger file input
                });
                addImageBtn.setAttribute('data-listener', 'true'); // Mark listener added
            }

            if (!imageInput.hasAttribute('data-listener')) {
                imageInput.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            console.log('hi')
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100%';
                            img.style.margin = '10px 0';
                            const textEditor = document.querySelector('.text-editor'+id);
                            if (textEditor) textEditor.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
                imageInput.setAttribute('data-listener', 'true'); // Mark listener added
            }
        }

        function makeUnderline() {
            document.execCommand('underline');
        }

        function changeFontSize(id) {
            const fontSize = document.getElementById('fontSize'+id).value;
            if (fontSize) {
                document.execCommand('fontSize', false, fontSize);
            }
        }

        function changeFontColor(id) {
            const fontColor = document.getElementById('fontColor'+id).value;
            if (fontColor) {
                document.execCommand('foreColor', false, fontColor);
            }
        }
    </script>

    <script>
        function followingFunc(user_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/following',
                type: 'POST',
                data: {
                    user_id: user_id,
                },
                success: function(response) {
                    const follow = `<i class="fa-solid fa-user-plus"></i> Follow`
                    const following = `<i class="fa-solid fa-user-plus"></i> Following`
                    if(response == 'following') {
                        $('.commonFollowFollowingStatus'+user_id).html(following);
                        $('.commonFollowFollowingStatus'+user_id).css('color', '#f59608');
                    }else {
                        $('.commonFollowFollowingStatus'+user_id).html(follow);
                        $('.commonFollowFollowingStatus'+user_id).css('color', '#fff');
                    }
                }
            });
        }
    </script>

    <script>
        async function copyLinkFunc(id) {
            const copyInputLink = document.querySelector('.copyInput'+id).value

            try {
                await navigator.clipboard.writeText(copyInputLink);
                Toastify({
                    text: "Link copied to clipboard!",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
            } catch (err) {
                Toastify({
                    text: "Failed to copy the link.",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "red",
                    stopOnFocus: true,
                }).showToast();
            }
        }
    </script>

    <script>
        function deleteQuestion(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your Favourite Question has been deleted.",
                        icon: "success"
                    });
                }
            });
        }
    </script>

    <script>
        function deleteFavouritePost(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your Favourite Question has been deleted.",
                        icon: "success"
                    });
                }
            });
        }
    </script>

    <script>
        function deleteFavouriteBlog(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your Favourite Blog has been deleted.",
                        icon: "success"
                    });
                }
            });
        }
    </script>

    <script>
        function videoPlayBtn(id) {
            const video = document.getElementById('video'+id);
            if (video) {
                const playButton = $('.play' + id);
                if (playButton.hasClass('fa-pause')) {
                    video.pause();
                    playButton.removeClass('fa-pause').addClass('fa-play');
                } else {
                    video.play();
                    playButton.removeClass('fa-play').addClass('fa-pause');
                }
            } else {
                console.error('Video element with id "video' + id + '" not found.');
            }

            const progressBar = document.getElementById('progress'+id);
            const currentTimeDisplay = document.getElementById('currentTime'+id);
            const totalDurationDisplay = document.getElementById('totalDuration'+id);

            totalDurationDisplay.textContent = formatTime(video.duration)

            video.addEventListener('timeupdate', () => {
                const progress = (video.currentTime / video.duration) * 100;
                progressBar.value = progress;
                currentTimeDisplay.textContent = formatTime(video.currentTime);
                progressBar.style.background = `linear-gradient(to right, #4caf50 0%, #4caf50 ${progress}%, #ddd 0, #ddd 100%)`
                if(video.currentTime == video.duration) {
                    const playButton = $('.play' + id);
                    video.pause();
                    playButton.removeClass('fa-pause').addClass('fa-play');
                }
            });

            progressBar.addEventListener('input', updateProgressBar);
            progressBar.addEventListener('change', updateProgressBar);

            function updateProgressBar(event) {
                const time = (event.target.value / 100) * video.duration;
                video.currentTime = time;
                const value = (progressBar.value - progressBar.min) / (progressBar.max - progressBar.min) * 100;

                progressBar.style.background = `linear-gradient(to right, #4caf50 0%, #4caf50 ${value}%, #ddd ${value}%, #ddd 100%)`;
            }
        }

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }

        function backwardButton(id) {
            const video = document.getElementById('video'+id);
            if (video) {
                video.currentTime -= 10;
            }else {
                onsole.error('Video element with id "video' + id + '" not found.');
            }
        }

        function forwardButton(id) {
            const video = document.getElementById('video'+id);
            if (video) {
                video.currentTime += 10;
            }else {
                onsole.error('Video element with id "video' + id + '" not found.');
            }
        }
    </script>

    <script>
        function deleteFavouriteVideo(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your Favourite Video has been deleted.",
                        icon: "success"
                    });
                }
            });
        }
    </script>

    <script>
        function followingFunc(user_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/following',
                type: 'POST',
                data: {
                    user_id: user_id,
                },
                success: function(response) {
                    if(response == 'following') {
                        $('.commonFollowFollowingStatus'+user_id).text('Following');
                    }else {
                        $('.commonFollowFollowingStatus'+user_id).text('Follow');
                    }
                }
            });
        }
    </script>
@endsection
