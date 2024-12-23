@extends('Frontend.navber.navber')


@section('content')
    <div id="modal" class="modal">
        <div class="modal-content">
            <i id="closeQuestionModalBtn" class="fa-solid fa-square-xmark question-close-btn"></i>
            <h4>@lang('messages.write-question')</h4>
            <form action="{{ route('question.store') }}" method="POST">
                @csrf
                <textarea name="question"></textarea>
                <div class="my-2 text-center">
                    @if (Auth::guard('user')->user())
                        <button type="submit">@lang('messages.publish')</button>
                    @else
                        <a href="{{ route('login') }}"><button type="button">@lang('messages.publish')</button></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div id="modal2" class="modal">
        <div class="modal-content list">
            <i id="closeModalBtn2" class="fa-solid fa-square-xmark question-close-btn"></i>
            <h4>@lang('messages.your-question-list')</h4>
            @if (Auth::guard('user')->user())
                <div class="question-list-container">
                    @forelse (App\Models\Question::where('user_id', Auth::guard('user')->user()->id)->latest()->get() as $my_question)
                        <div class="question-list-item">
                            <a href="{{ route('delete.question', $my_question->id) }}">
                                <i class="fa-solid fa-trash my-question-delete-btn"></i>
                            </a>
                            <h6>{{ $my_question->question }}</h6>
                            <button onclick="open_my_question_answer({{ $my_question->id }})">@lang('messages.answers') ({{ $my_question->rel_to_answer->count() }})</button>

                            <div class="my-answer-list-container my-answer-list-container{{ $my_question->id }}">
                                @forelse (App\Models\Answer::where('question_id', $my_question->id)->latest()->get() as $answer)
                                    <div class="answers-item">
                                        <div class="answer-user-profile">
                                            @if ($answer->rel_to_user->photo == null)
                                                <img class="answer-user-profile-img" src="{{ Avatar::create($answer->rel_to_user->fname.' '.$answer->rel_to_user->lname)->toBase64() }}" />
                                            @else
                                                <img class="answer-user-profile-img" src="{{ asset('upload/users') }}/{{ $answer->rel_to_user->photo }}" alt="" />
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
                            <div class="empty-question-box">
                                <img src="{{ asset('assets/box.png') }}" alt="">
                                <h6>Empty</h6>
                            </div>
                        @endforelse
                </div>
            @else
                <a class="btn btn-success my-5" href="{{ route('login') }}">Login</a>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="question-input-container">
                <h5>@lang('messages.question-head')</h5>
                <div class="question-input-field">
                    <div class="question-add-btn" id="openModalBtn">
                        <i class="fa-solid fa-circle-plus"></i>
                        <button>@lang('messages.question')</button>
                    </div>
                    <div class="question-list-btn" id="openListModelBtn">
                        <i class="fa-regular fa-rectangle-list"></i>
                        <button>@lang('messages.question-list')</button>
                    </div>
                </div>
                <img class="question-png" src="{{ asset('assets/question.png') }}" alt="">
                @if (session('deleted_question'))
                    <p>{{ session('deleted_question') }}</p>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="question-view-container">
                        @foreach ($questions as $question)
                            <div class="question-viewed question-viewed{{ $question->id }}">
                                @if (Auth::guard('user')->user())
                                    <i onclick="hideQuestion({{ $question->id }})" class="fa-solid fa-rectangle-xmark hide-question-btn"></i>
                                @else
                                    <a href="{{ route('login') }}">
                                        <i class="fa-solid fa-rectangle-xmark hide-question-btn"></i>
                                    </a>
                                @endif
                                <h6>{{ $question->question }}</h6>
                                <p class="question-viewed-btn" onclick="open_answer_list({{ $question->id }})">@lang('messages.answers') ({{ $question->rel_to_answer->count() }})</p>
                                <div class="question-action">
                                    <button class="answer-btn" onclick="open_answer_box({{ $question->id }})">
                                        <i class="fa-solid fa-pen-to-square"></i> @lang('messages.answer')
                                    </button>
                                    @if ( Auth::guard('user')->user())
                                        @php
                                            $isFollowing = App\Models\Following_Follower::where('follower', $question->rel_to_user->id)
                                                ->where('following', Auth::guard('user')->user()->id)
                                                ->exists();
                                        @endphp
                                    @endif
                                    @if (Auth::guard('user')->user())
                                        <button style="color: {{ $isFollowing ? '#f59608' : '' }}" class="commonFollowFollowingStatus{{ $question->rel_to_user->id }}" onclick="followingFunc({{ $question->rel_to_user->id }})">
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
                                    <button onclick="copyLinkFunc({{ $question->id }})"><i class="fa-regular fa-copy"></i> @lang('messages.link')</button>
                                    @if (Auth::guard('user')->user())
                                        <button onclick="addFavourite({{ $question->id }})">
                                            @if (App\Models\Favourite::where('user_id', Auth::guard('user')->user()->id)->where('question_id', $question->id)->exists())
                                                <i style="color: #ae00ff" class="fa-solid fa-heart commonFavouriteColor{{ $question->id }}"></i>
                                                @lang('messages.favourite')
                                            @else
                                                <i class="fa-solid fa-heart commonFavouriteColor{{ $question->id }}"></i>
                                                @lang('messages.favourite')
                                            @endif
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <button>
                                                <i class="fa-solid fa-heart commonFavouriteColor{{ $question->id }}"></i>
                                                @lang('messages.favourite')
                                            </button>
                                        </a>
                                    @endif
                                    <input type="text" class="copyInput{{ $question->id }}" value="{{ config('app.url') }}/question/{{ $question->slug }}" hidden>
                                </div>
                                <div class="answer-input-box answer-input-box{{ $question->id }}">
                                    <form id="answerEditorForm{{ $question->id }}" onsubmit="handleSubmit({{ $question->id }})" action="{{ route('answer.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="text-editor-main">
                                            <div contenteditable="true" id="text-editor" class="text-editor{{ $question->id }}"></div>
                                            <div class="text-editor-action-box">
                                                <button type="button" onclick="makeBold()">Bold</button>
                                                <button type="button" onclick="makeItalic()">Italic</button>
                                                <button type="button" id="addImageBtn{{ $question->id }}" onclick="addImageFunc({{ $question->id }})">Add Image</button>
                                                <input type="file" class="imageInput{{ $question->id }}" accept="image/*" hidden>
                                                <button type="button" onclick="makeUnderline()">Underline</button>
                                                <select id="fontSize{{ $question->id }}" onchange="changeFontSize({{ $question->id }})">
                                                    <option value="">Font Size</option>
                                                    <option value="1">10px</option>
                                                    <option value="2">13px</option>
                                                    <option value="3">16px</option>
                                                    <option value="4">18px</option>
                                                    <option value="5">24px</option>
                                                    <option value="6">32px</option>
                                                    <option value="7">48px</option>
                                                </select>
                                                <input type="color" id="fontColor{{ $question->id }}" onchange="changeFontColor({{ $question->id }})" title="Choose Font Color">
                                                <input type="hidden" name="answer" id="answerEditorContent{{ $question->id }}">
                                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                            </div>
                                        </div>
                                        @if (Auth::guard('user')->user())
                                            <button class="answer-input-btn" type="submit">@lang('messages.answer')</button>
                                        @else
                                            <a href="{{ route('login') }}">
                                                <button class="answer-input-btn" type="button">@lang('messages.answer')</button>
                                            </a>
                                        @endif
                                    </form>
                                </div>
                                <div class="answers-container answers-container{{ $question->id }}">
                                    @forelse (App\Models\Answer::where('question_id', $question->id)->latest()->get() as $answer)
                                        <div class="answers-item">
                                            <div class="answer-user-profile">
                                                <a href="{{ route('user.profile', $answer->rel_to_user->slug) }}">
                                                    @if ($answer->rel_to_user->photo == null)
                                                        <img class="answer-user-profile-img" src="{{ Avatar::create($answer->rel_to_user->fname.' '.$answer->rel_to_user->lname)->toBase64() }}" />
                                                    @else
                                                        @if (filter_var($answer->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                            <img class="answer-user-profile-img" src="{{ $answer->rel_to_user->photo }}" alt="" />
                                                        @else
                                                            <img class="answer-user-profile-img" src="{{ asset('upload/users') }}/{{ $answer->rel_to_user->photo }}" alt="" />
                                                        @endif
                                                    @endif
                                                </a>
                                                <div class="answer-user-name">
                                                    <a href="{{ route('user.profile', $answer->rel_to_user->slug) }}"><h5>{{ $answer->rel_to_user->fname }} {{ $answer->rel_to_user->lname }}</h5></a>
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
                        @endforeach
                    </div>
                </div>
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
        const openModalBtn = document.getElementById('openModalBtn');
        const question_closeModalBtn = document.getElementById('closeQuestionModalBtn');
        const question_modal = document.getElementById('modal');
        openModalBtn.addEventListener('click', () => {
            question_modal.style.display = 'flex';
            setTimeout(() => {
                question_modal.classList.add('show');
            }, 10);
        });
        question_closeModalBtn.addEventListener('click', () => {
            question_modal.classList.remove('show');
            setTimeout(() => {
                question_modal.style.display = 'none';
            }, 300);
        });
        question_modal.addEventListener('click', (event) => {
            if (event.target === question_modal) {
                question_closeModalBtn.click();
            }
        });
    </script>

    <script>
        const openListModelBtn = document.getElementById('openListModelBtn');
        const closeModalBtn2 = document.getElementById('closeModalBtn2');
        const modal2 = document.getElementById('modal2');
        openListModelBtn.addEventListener('click', () => {
            modal2.style.display = 'flex';
            setTimeout(() => {
                modal2.classList.add('show');
            }, 10);
        });
        closeModalBtn2.addEventListener('click', () => {
            modal2.classList.remove('show');
            setTimeout(() => {
                modal2.style.display = 'none';
            }, 300);
        });
        modal2.addEventListener('click', (event) => {
            if (event.target === modal2) {
                closeModalBtn2.click();
            }
        });
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
                    const follow = `<i class="fa-solid fa-user-plus"></i> @lang('messages.follow')`
                    const following = `<i class="fa-solid fa-user-plus"></i> @lang('messages.following')`
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
        function addFavourite(question_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/add/favourite',
                type: 'POST',
                data: {
                    type: 'question',
                    question_id: question_id,
                },
                success: function(response) {
                    if(response == 'favourite') {
                        $('.commonFavouriteColor'+question_id).css('color', '#ae00ff')
                    }else {
                        $('.commonFavouriteColor'+question_id).css('color', 'white')
                    }
                }
            });
        }

        function hideQuestion(id) {
            $('.question-viewed'+id).css('display', 'none')
        }
    </script>
@endsection
