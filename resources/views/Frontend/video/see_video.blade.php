@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="video-container">
                        <div class="video-item">
                            <div class="video-user-profile">
                                <a href="{{ route('user.profile', $video->rel_to_user->slug) }}">
                                    @if ($video->rel_to_user->photo == null)
                                        <img class="user-profile" src="{{ Avatar::create($video->rel_to_user->fname.' '.$video->rel_to_user->lname)->toBase64() }}" />
                                    @else
                                        @if (filter_var($video->rel_to_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $video->rel_to_user->photo }}" alt="">
                                        @else
                                            <img src="{{ asset('upload/users') }}/{{ $video->rel_to_user->photo }}" alt="">
                                        @endif
                                    @endif
                                </a>
                                <div class="video-user-name">
                                    <h5><a href="{{ route('user.profile', $video->rel_to_user->slug) }}">{{ $video->rel_to_user->fname }} {{ $video->rel_to_user->lname }}</a>
                                        <span onclick="followingFunc({{ $video->rel_to_user->id }})" class="commonFollowFollowingStatus{{ $video->rel_to_user->id }}">
                                            @if (Auth::guard('user')->user())
                                                @if (App\Models\Following_Follower::where('follower', $video->rel_to_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                                    @lang('messages.following')
                                                @else
                                                    @lang('messages.follow')
                                                @endif
                                            @else
                                                <a style="color: green" href="{{ route('login') }}">@lang('messages.follow')</a>
                                            @endif
                                        </span>
                                    </h5>
                                    <h6>{{ $video->created_at->diffForHumans() }}</h6>
                                </div>
                            </div>
                            <div class="video-content">{!! $video->video_content !!}</div>
                            <div class="video-div">
                                <div class="play-next-prev-btns">
                                    <i onclick="backwardButton({{ $video->id }})" class="fa-solid fa-arrow-rotate-left"><span>10</span></i>
                                    <i onclick="videoPlayBtn({{ $video->id }})" class="fa-solid fa-play play-btn play{{  $video->id }}"></i>
                                    <i onclick="forwardButton({{ $video->id }})" class="fa-solid fa-arrow-rotate-right"><span>10</span></i>
                                </div>
                                <div class="video-range-time">
                                    <div class="time-and-time">
                                        <span id="currentTime{{ $video->id }}">0:00</span>
                                        <span id="totalDuration{{ $video->id }}">0:00</span>
                                    </div>
                                    <input type="range" id="progress{{ $video->id }}" value="0" max="100">
                                </div>
                                <div class="video-actions">
                                    @if (Auth::guard('user')->user())
                                        @if (App\Models\Like::where('user_id', Auth::guard('user')->user()->id)->where('video_id', $video->id)->first())
                                            <i onclick="liked({{ $video->id }}, {{ $video->rel_to_user->id }})" class="fa-solid fa-thumbs-up active commonLikeColor{{ $video->id }}"></i>
                                        @else
                                            <i onclick="liked({{ $video->id }}, {{ $video->rel_to_user->id }})" class="fa-solid fa-thumbs-up commonLikeColor{{ $video->id }}"></i>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"> <i class="fa-solid fa-thumbs-up"></i></a>
                                    @endif

                                    @if (Auth::guard('user')->user())
                                        @if (App\Models\Comment::where('video_id', $video->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                                            <i onclick="toggleCommentSection({{ $video->id }})" class="fa-solid fa-comment comment-active commonCommentColor{{ $video->id }}"></i>
                                        @else
                                            <i onclick="toggleCommentSection({{ $video->id }})" class="fa-solid fa-comment commonCommentColor{{ $video->id }}"></i>
                                        @endif
                                    @else
                                        <i onclick="toggleCommentSection({{ $video->id }})" class="fa-solid fa-comment commonCommentColor{{ $video->id }}"></i>
                                    @endif

                                    <i onclick="copyLinkFunc({{ $video->id }})" class="fa-solid fa-copy commonShareColor{{ $video->id }}"></i>

                                    @if (Auth::guard('user')->user())
                                        @if (App\Models\Favourite::where('user_id', Auth::guard('user')->user()->id)->where('video_id', $video->id)->first())
                                            <i onclick="addFavourite({{ $video->id }})" class="fa-solid fa-heart active commonFavouriteColor{{ $video->id }}"></i>
                                        @else
                                            <i onclick="addFavourite({{ $video->id }})" class="fa-solid fa-heart commonFavouriteColor{{ $video->id }}"></i>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"><i class="fa-solid fa-heart"></i></a>
                                    @endif
                                    <input type="text" class="copyInput{{ $video->id }}" value="{{ config('app.url') }}/video/{{ $video->slug }}" hidden>
                                </div>
                                <video id="video{{ $video->id }}" class="lazy" data-src="{{ asset('upload/videos') }}/{{ $video->video_name }}" src=""></video>
                            </div>

                            <div class="comment-container video-padding-right comment-container{{ $video->id }} mt-3">
                                <div class="commetn-input-box">
                                    <form>
                                        <textarea class="comment_text{{ $video->id }}" name="comment_text" placeholder="Write Comment...."></textarea>
                                        @if (Auth::guard('user')->user())
                                            <button type="button" onclick="sendComment({{ $video->id }}, {{ $video->rel_to_user->id }})">
                                                <img src="{{ asset('assets/send.png') }}" alt="">
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}">
                                                <button type="button">
                                                    <img src="{{ asset('assets/send.png') }}" alt="">
                                                </button>
                                            </a>
                                        @endif
                                    </form>
                                </div>
                                <ul id="comments-list{{ $video->id }}" class="comments-list" >
                                    @foreach (App\Models\Comment::where('video_id', $video->id)->latest()->get() as $comment)
                                        <li class="comment_item{{ $comment->id }}">
                                            <div class="comment-main-level">
                                                <div class="comment-avatar">
                                                    @if ($comment->rel_to_user->photo == null)
                                                        <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">
                                                            <img id="blah" class="user-profile" src="{{ Avatar::create($comment->rel_to_user->fname.' '.$comment->rel_to_user->lname)->toBase64() }}" />
                                                        </a>
                                                    @else
                                                        <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">
                                                            @if (filter_var($comment->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                <img src="{{ $comment->rel_to_user->photo }}" alt="">
                                                            @else
                                                                <img src="{{ asset('upload/users') }}/{{ $comment->rel_to_user->photo }}" alt="">
                                                            @endif
                                                        </a>
                                                    @endif

                                                </div>
                                                <div class="comment-box">
                                                    <div class="comment-head">
                                                        <h6 class="comment-name">
                                                            <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">{{ $comment->rel_to_user->fname }} {{ $comment->rel_to_user->lname }}
                                                            </a>
                                                        </h6>
                                                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                                                        <i class="fa fa-reply" onclick="sendReply({{ $video->id }},{{ $comment->id }}, {{ $comment->rel_to_user->id }})"></i>
                                                        <i onclick="redirectToChatPage('{{ $comment->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                    </div>
                                                    <div class="comment-content">
                                                        {{ $comment->comment }}
                                                    </div>
                                                </div>
                                            </div>

                                            <ul class="comments-list reply-list reply-list{{ $comment->id }}">
                                                @foreach (App\Models\Reply::where('video_id', $video->id)->where('comment_id', $comment->id)->where('status', 10)->latest()->get() as $reply10)
                                                    <li id="reply10_item{{ $reply10->id }}">
                                                        <div class="comment-main-level">
                                                            <div class="comment-avatar">
                                                                @if ($reply10->rel_to_user->photo == null)
                                                                    <a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">
                                                                        <img id="blah" class="user-profile" src="{{ Avatar::create($reply10->rel_to_user->fname.' '.$reply10->rel_to_user->lname)->toBase64() }}" />
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">
                                                                        @if (filter_var($reply10->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                            <img src="{{ $reply10->rel_to_user->photo }}" alt="">
                                                                        @else
                                                                            <img src="{{ asset('upload/users') }}/{{ $reply10->rel_to_user->photo }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div class="comment-box">
                                                                <div class="comment-head">
                                                                    <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">{{ $reply10->rel_to_user->fname }} {{ $reply10->rel_to_user->lname }}</a></h6>
                                                                    <span>{{ $reply10->created_at->diffForHumans() }}</span>
                                                                    <i class="fa fa-reply" onclick="sendReply20({{ $video->id }},{{ $comment->id }}, {{ $reply10->id }}, {{ $reply10->rel_to_user->id }})"></i>
                                                                    <i onclick="redirectToChatPage('{{ $reply10->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                                </div>
                                                                <div class="comment-content">
                                                                    {{ $reply10->reply }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <ul class="comments-list reply-list reply-list10{{ $reply10->id }}">
                                                            @foreach (App\Models\Reply_20::where('video_id', $video->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->latest()->get() as $reply20)
                                                                <li id="reply20_item{{ $reply20->id }}">
                                                                    <div class="comment-main-level">
                                                                        <div class="comment-avatar">
                                                                            @if ($reply20->rel_to_user->photo == null)
                                                                                <a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">
                                                                                    <img id="blah" class="user-profile" src="{{ Avatar::create($reply20->rel_to_user->fname.' '.$reply20->rel_to_user->lname)->toBase64() }}" />
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">
                                                                                    @if (filter_var($reply20->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                                        <img src="{{ $reply20->rel_to_user->photo }}" alt="">
                                                                                    @else
                                                                                        <img src="{{ asset('upload/users') }}/{{ $reply20->rel_to_user->photo }}" alt="">
                                                                                    @endif
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                        <div class="comment-box">
                                                                            <div class="comment-head">
                                                                                <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">{{ $reply20->rel_to_user->fname }} {{ $reply20->rel_to_user->lname }}</a></h6>
                                                                                <span>{{ $reply20->created_at->diffForHumans() }}</span>
                                                                                <i class="fa fa-reply" onclick="sendReply30({{ $video->id }},{{ $comment->id }}, {{ $reply20->id }}, {{ $reply10->id }}, {{ $reply20->rel_to_user->id }})"></i>
                                                                                <i onclick="redirectToChatPage('{{ $reply20->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                                            </div>
                                                                            <div class="comment-content">
                                                                                {{ $reply20->reply }}
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <ul class="comments-list reply-list reply-list20{{ $reply20->id }}">
                                                                        @foreach (App\Models\Reply_30::where('video_id', $video->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->where('reply20', $reply20->id)->latest()->get() as $reply30)
                                                                            <li id="reply30_item{{ $reply30->id }}">
                                                                                <div class="comment-main-level">
                                                                                    <div class="comment-avatar">
                                                                                        @if ($reply30->rel_to_user->photo == null)
                                                                                            <a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">
                                                                                                <img id="blah" class="user-profile" src="{{ Avatar::create($reply30->rel_to_user->fname.' '.$reply30->rel_to_user->lname)->toBase64() }}" />
                                                                                            </a>
                                                                                        @else
                                                                                            <a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">
                                                                                                @if (filter_var($reply30->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                                                    <img src="{{ $reply30->rel_to_user->photo }}" alt="">
                                                                                                @else
                                                                                                    <img src="{{ asset('upload/users') }}/{{ $reply30->rel_to_user->photo }}" alt="">
                                                                                                @endif
                                                                                            </a>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="comment-box">
                                                                                        <div class="comment-head">
                                                                                            <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">{{ $reply30->rel_to_user->fname }} {{ $reply30->rel_to_user->lname }}</a></h6>
                                                                                            <span>{{ $reply30->created_at->diffForHumans() }}</span>
                                                                                            <i onclick="redirectToChatPage('{{ $reply30->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                                                        </div>
                                                                                        <div class="comment-content">
                                                                                            {{ $reply30->reply }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
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
    @include('Frontend.layout.right_side_blogs')
@endsection


@section('footer_script')
    <script>
        var lazyLoadInstance = new LazyLoad({
            elements_selector: ".lazy"
        });

        document.addEventListener("DOMContentLoaded", function() {
            const lazyImages = document.querySelectorAll("img.lazy");

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove("lazy");
                        observer.unobserve(image);
                    }
                });
            });
            lazyImages.forEach(image => observer.observe(image));
        });
    </script>

    <script>
        function defaultComment(response) {
            return `
                @if (Auth::guard('user')->user())
                    <li>
                        <div class="comment-main-level">
                            <div class="comment-avatar">
                                @if (Auth::guard('user')->user()->photo == null)
                                    <img id="blah" class="user-profile" src="{{ Avatar::create(Auth::guard('user')->user()->fname.' '.Auth::guard('user')->user()->lname)->toBase64() }}" />
                                @else
                                    @if (filter_var(Auth::guard('user')->user()->photo, FILTER_VALIDATE_URL))
                                        <img class="user-profile" src="{{ Auth::guard('user')->user()->photo }}" alt="" />
                                    @else
                                        <img class="user-profile" src="{{ asset('upload/users') }}/{{ Auth::guard('user')->user()->photo }}" alt="" />
                                    @endif
                                @endif
                            </div>
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name"><a href="{{ route('user.profile', Auth::guard('user')->user()->slug) }}">{{ Auth::guard('user')->user()->fname.' '.Auth::guard('user')->user()->lname }}</a></h6>
                                    <span>now</span>
                                    <i class="fa fa-reply"></i>
                                    <i onclick="redirectToChatPage('{{  Auth::guard('user')->user()->slug }}')" class="fa-solid fa-comment-dots"></i>
                                </div>
                                <div class="comment-content">
                                    ${response}
                                </div>
                            </div>
                        </div>
                    </li>
                @endif`
        }

        function replyFunction(reply_main, click, video_id = null, comment_id = null, video_user_id = null, reply10_id = null, reply20_id = null, value_id = null) {
            if (reply_main.next('.reply-input-box').length === 0) {
                const replyInputBox = `
                    <div class="reply-input-box">
                        <form>
                            <textarea class="comment_text reply_text${value_id}" name="reply_text" placeholder="Reply...."></textarea>
                            @if (Auth::guard('user')->user())
                                <button type="button" onclick="${click}(${video_id}, ${comment_id}, ${video_user_id}, ${reply10_id}, ${reply20_id})">
                                    <img src="/assets/send.png" alt="">
                                </button>
                            @else
                                <a href="{{ route('login') }}">
                                    <button type="button">
                                        <img src="/assets/send.png" alt="">
                                    </button>
                                </a>
                            @endif
                        </form>
                    </div>`;
                reply_main.after(replyInputBox);
            } else {
                reply_main.next('.reply-input-box').remove();
            }
            commonStyle();
        }

        function particle(container, class_name) {
            for (let i = 0; i < 40; i++) {
                const particle = document.createElement('div');
                particle.classList.add(class_name);
                container.appendChild(particle);
                gsap.fromTo(particle,
                    {
                        x: 0,
                        y: 0,
                        opacity: 1,
                        scale: 1,
                    },
                    {
                        x: Math.random() * 200 - 100,
                        y: Math.random() * 200 - 100,
                        opacity: 0,
                        scale: 0,
                        duration: 1.5,
                        onComplete: () => container.removeChild(particle),
                    }
                );
            }
        }

        function toastify(message, bg_color) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: bg_color,
                stopOnFocus: true,
            }).showToast();
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
        const parentListItems = document.querySelectorAll('.comments-list > li');
        parentListItems.forEach(item => {
            const nestedUl = item.querySelector('ul');

            if (!nestedUl) {
                item.className = 'removeNullReplyBox'
            }
        });
        function setAfterHeight(Ul) {
            if (Ul.lastElementChild) { // Check if there is a last child
                const child = Ul.lastElementChild;
                const parentElement = child.parentElement.parentElement;

                const height = parentElement.offsetHeight - child.offsetHeight - 15;
                parentElement.style.setProperty('--after-height', `${height}px`);
            }
        }
        const commentListsUl = document.querySelectorAll('.comments-list li');
        commentListsUl.forEach(Ul => {
            setAfterHeight(Ul);
        });

        function commonStyle() {
            const parentListItems = document.querySelectorAll('.comments-list > li');
            parentListItems.forEach(item => {
                const nestedUl = item.querySelector('ul');
                if (!nestedUl) {
                    item.className = 'removeNullReplyBox'
                }
            });
            function setAfterHeight(Ul) {
                if (Ul.lastElementChild) { // Check if there is a last child
                    const child = Ul.lastElementChild;
                    const parentElement = child.parentElement.parentElement;
                    const height = parentElement.offsetHeight - child.offsetHeight - 15;
                    parentElement.style.setProperty('--after-height', `${height}px`);
                }
            }
            const commentListsUl = document.querySelectorAll('.comments-list li ul');
            commentListsUl.forEach(Ul => {
                setAfterHeight(Ul);
            });
        }

        window.addEventListener('resize', () => {
            commonStyle()
        })
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
                        $('.commonFollowFollowingStatus'+user_id).text('@lang('messages.following')');
                    }else {
                        $('.commonFollowFollowingStatus'+user_id).text('@lang('messages.follow')');
                    }
                }
            });
        }
    </script>

    <script>
        function liked(videoID, video_user_id) {
            const container = document.querySelector('.commonLikeColor'+videoID);
            particle(container, 'like-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/video/like',
                type: 'POST',
                data: {
                    video_id: videoID,
                    video_user_id: video_user_id,
                },
                success: function(response) {
                    if(response == 'like') {
                        $('.commonLikeColor'+videoID).css('color', '#13fdc3')
                        $('.commonLikeColor'+videoID).css('background', '#05f8af4d')
                    }else {
                        $('.commonLikeColor'+videoID).css('color', 'burlywood')
                        $('.commonLikeColor'+videoID).css('background', 'transparent')
                    }
                }
            });
        }
    </script>

    <script>
        function addFavourite(video_id) {
            const container = document.querySelector('.commonFavouriteColor'+video_id);
            particle(container, 'favourite-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/add/favourite',
                type: 'POST',
                data: {
                    type: 'video',
                    video_id: video_id,
                },
                success: function(response) {
                    if(response == 'favourite') {
                        $('.commonFavouriteColor'+video_id).css('color', '#ae00ff')
                        $('.commonFavouriteColor'+video_id).css('background', '#ae00ff52')
                    }else {
                        $('.commonFavouriteColor'+video_id).css('color', 'burlywood')
                        $('.commonFavouriteColor'+video_id).css('background', 'transparent')
                    }
                }
            });
        }
    </script>

    <script>
        async function copyLinkFunc(id) {
            const copyInputLink = document.querySelector('.copyInput'+id).value
            const container = document.querySelector('.commonShareColor'+id);
            particle(container, 'share-particle')

            try {
                await navigator.clipboard.writeText(copyInputLink);
                toastify("Link copied to clipboard!", "green")
            } catch (err) {
                toastify("Failed to copy the link.", "red")
            }
        }
    </script>

    <script>
        function toggleCommentSection(id) {
            const container = document.querySelector('.commonCommentColor'+id);
            particle(container, 'comment-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/update/comment/status',
                data: {'post_id': id},
                success: function(data) {
                    $('.comment-container'+id).fadeToggle(500, 'swing');
                    commonStyle()
                }
            })
        }
    </script>

    <script>
        function sendComment(video_id, video_user_id) {
            const commentText = $('.comment_text'+video_id).val();

            if(commentText.trim() === "") {
                toastify("Please, write something", "pink")
                return
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/video/comment/store',
                type: 'POST',
                data: {
                    video_id: video_id,
                    video_user_id: video_user_id,
                    comment_text: commentText,
                },
                success: function(response) {
                    let comment = defaultComment(response)
                    $('.comment_text'+video_id).val('');
                    $('#comments-list'+video_id).prepend(comment);
                },
            });
        }
    </script>

    <script>
        function sendReply(video_id, comment_id, video_user_id) {
            const commentDiv = $(".comment_item" + comment_id + " > div")
            replyFunction(commentDiv, 'sendReplyStore', video_id, comment_id, video_user_id, null, null, comment_id);
        }

        function sendReplyStore(video_id, comment_id, video_user_id) {
            const replyText = $('.reply_text'+comment_id).val();

            if(replyText.trim() === "") {
                toastify("Please, reply something", "pink")
                return
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/video/reply/store',
                type: 'POST',
                data: {
                    video_id: video_id,
                    comment_id: comment_id,
                    video_user_id: video_user_id,
                    reply_text: replyText,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+comment_id).val('')
                    $('.reply-list'+comment_id).prepend(replyNow);

                    commonStyle()
                },
            });
        }
    </script>

    <script>
        function sendReply20(video_id, comment_id, reply10_id, video_user_id) {
            const replyItemList = $("#reply10_item"+reply10_id+" > div");
            replyFunction(replyItemList, 'sendReply20Store', video_id, comment_id, video_user_id, reply10_id, null, reply10_id);
        }

        function sendReply20Store(video_id, comment_id, video_user_id, reply10_id) {
            const reply20Text = $('.reply_text'+reply10_id).val();
            if(reply20Text.trim() === "") {
                toastify("Please, reply something", "pink")
                return
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/video/reply/store',
                type: 'POST',
                data: {
                    video_id: video_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    video_user_id: video_user_id,
                    value: 10,
                    reply_text: reply20Text,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+reply10_id).val('')
                    $('.reply-list10'+reply10_id).prepend(replyNow);
                    commonStyle()
                },
            });
        }
    </script>

    <script>
        function sendReply30(video_id, comment_id, reply20_id, reply10_id, video_user_id) {
            const reply30ItemList = $("#reply20_item"+reply20_id+" > div");
            replyFunction(reply30ItemList, 'sendReply30Store', video_id, comment_id, video_user_id, reply10_id, reply20_id, reply20_id);
        }

        function sendReply30Store(video_id, comment_id, video_user_id, reply10_id, reply20_id) {
            const reply30Text = $('.reply_text'+reply20_id).val();
            if(reply30Text.trim() === "") {
                toastify("Please, reply something", "pink")
                return
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/video/reply/store',
                type: 'POST',
                data: {
                    video_id: video_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    video_user_id: video_user_id,
                    value: 20,
                    reply20_id: reply20_id,
                    reply_text: reply30Text,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+reply20_id).val('')
                    $('.reply-list20'+reply20_id).prepend(replyNow);
                    commonStyle()
                },
            });
        }
    </script>

    <script>
        function redirectToChatPage(userSlug) {
            window.location.href = `/message?user=${userSlug}`;
        }
    </script>
@endsection
