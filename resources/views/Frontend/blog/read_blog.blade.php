@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="modal modal2">
                <div class="modal-content for-likes">
                    <i id="closeModalBtn2" class="fa-solid fa-square-xmark question-close-btn"></i>
                    <ul class="post-likers">
                        @forelse (App\Models\Like::where('blog_id', $blog->id)->latest()->get() as $like_user)
                            <li>
                                <a href="{{ route('user.profile', $like_user->rel_to_like_user->slug) }}">
                                    @if ($like_user->rel_to_like_user->photo == null)
                                        <img src="{{ Avatar::create($like_user->rel_to_like_user->fname.' '.$like_user->rel_to_like_user->lname)->toBase64() }}" />
                                    @else
                                        @if (filter_var($like_user->rel_to_like_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $like_user->rel_to_like_user->photo }}" alt="" />
                                        @else
                                            <img src="{{ asset('upload/users') }}/{{ $like_user->rel_to_like_user->photo }}" alt="" />
                                        @endif
                                    @endif
                                    <div class="liker-user-name">
                                        {{ Str::limit($like_user->rel_to_like_user->fname.' '.$like_user->rel_to_like_user->lname, 20) }}
                                        <h6>
                                            @lang('messages.follower'): {{ $like_user->rel_to_like_user->rel_to_follower->count() }}
                                        </h6>
                                    </div>
                                </a>
                                <button class="commonFollowFollowingStatus{{ $like_user->rel_to_like_user->id }}" onclick="followingFunc({{ $like_user->rel_to_like_user->id }})">
                                    @if (App\Models\Following_Follower::where('follower', $like_user->rel_to_like_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                        @lang('messages.following')
                                    @else
                                        @lang('messages.follow')
                                    @endif
                                </button>
                            </li>
                        @empty
                            <li style="text-align: center">There are not like yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="read-blog-container">
                <div class="read-blog-banner">
                    <a href="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" data-lightbox="image"><img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt=""></a>
                    <h3>{{ $blog->blog_title }}</h3>
                </div>
                <div class="read-blog-content">
                    <div class="blog-writer-profile">
                        <a href="{{ route('user.profile', $blog->rel_to_user->slug) }}">
                            @if ($blog->rel_to_user->photo == null)
                                <img src="{{ Avatar::create($blog->rel_to_user->fname.' '.$blog->rel_to_user->lname)->toBase64() }}" />
                            @else
                                @if (filter_var($blog->rel_to_user->photo, FILTER_VALIDATE_URL))
                                    <img src="{{ $blog->rel_to_user->photo }}" alt="">
                                @else
                                    <img src="{{ asset('upload/users') }}/{{ $blog->rel_to_user->photo }}" alt="">
                                @endif
                            @endif
                        </a>
                        <div class="blog-writer-name">
                            <h5>
                                <a href="{{ route('user.profile', $blog->rel_to_user->slug) }}">{{ $blog->rel_to_user->fname.' '.$blog->rel_to_user->lname }}</a>
                                <span class="commonFollowFollowingStatus{{ $blog->rel_to_user->id }}" onclick="followingFunc({{ $blog->rel_to_user->id }})">
                                    @if (Auth::guard('user')->user())
                                        @if (App\Models\Following_Follower::where('follower', $blog->rel_to_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                            @lang('messages.following')
                                        @else
                                            @lang('messages.follow')
                                        @endif
                                    @else
                                        <a style="color: green" href="{{ route('login') }}">@lang('messages.follow')</a>
                                    @endif
                                </span>
                            </h5>
                            <h6>{{ $blog->created_at->diffForHumans() }}</h6>
                        </div>
                    </div>
                    <h4>{{ $blog->blog_title }}</h4>
                    <article>
                        {!! $blog->blog_content !!}
                    </article>
                </div>
            </div>
        </div>
        <div class="related-show-blog">
            <h5>@lang('messages.you-are-interested')</h5>
            <div class="row">
                @foreach ($show_related_blogs as $show_related_blog)
                    <div class="col-lg-6">
                        <a style="text-decoration: none" href="{{ route('read.blog', $show_related_blog->slug) }}">
                            <div class="explor-blog-2">
                                <img src="{{ asset('upload/blogs') }}/{{ $show_related_blog->blog_banner }}" alt="">
                                <h6>{{ Str::limit($show_related_blog->blog_title, 50, '...') }}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-12">
            <div class="post-actions blog-actions">
                <div class="like-comment">
                    <div>
                        @if (Auth::guard('user')->user())
                            @if (App\Models\Like::where('user_id', Auth::guard('user')->user()->id)->where('blog_id', $blog->id)->first())
                                <i class="fa-solid fa-thumbs-up commonLikeColor{{ $blog->id }} like-active" onclick="liked({{ $blog->id }}, {{ $blog->rel_to_user->id }})"></i>
                            @else
                                <i class="fa-solid fa-thumbs-up commonLikeColor{{ $blog->id }}" onclick="liked({{ $blog->id }}, {{ $blog->rel_to_user->id }})"></i>
                            @endif
                        @else
                            <a href="{{ route('login') }}"><i class="fa-solid fa-thumbs-up commonLikeColor"></i></a>
                        @endif
                        <span style="cursor: pointer" onclick="see_likes()">Liked ({{ $blog->rel_to_like_count->count() }})</span>
                        <span style="cursor: pointer" onclick="see_likes()" class="sm-like-count">({{ $blog->rel_to_like_count->count() }})</span>
                    </div>
                    <div onclick="blogCommentBox()" class="comment" id="comment" post-id="{{ $blog->id }}">
                        @if (Auth::guard('user')->user())
                            @if (App\Models\Comment::where('blog_id', $blog->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                                <i class="fa-solid fa-comment comment-active commentCommonColor{{ $blog->id }}"></i>
                            @else
                                <i class="fa-solid fa-comment commentCommonColor{{ $blog->id }}"></i>
                            @endif
                        @else
                            <a href="{{ route('login') }}"><i class="fa-solid fa-comment commentCommonColor{{ $blog->id }}"></i></a>
                        @endif
                        <span>Comments ({{ $blog->rel_to_blog_comments->count() + $blog->rel_to_blog_reply->count() + $blog->rel_to_blog_reply_20->count() + $blog->rel_to_blog_reply_30->count() }})</span>
                        <span class="sm-comment-count">({{ $blog->rel_to_blog_comments->count() + $blog->rel_to_blog_reply->count() + $blog->rel_to_blog_reply_20->count() + $blog->rel_to_blog_reply_30->count() }})</span>
                    </div>
                </div>
                <div class="blog-share">
                    <i onclick="copyLinkFunc()" class="fa-solid fa-copy commonCopyColor"></i>
                    <span>Copy Link</span>
                    <input type="text" class="copyInput" value="{{ config('app.url') }}/read/{{ $blog->slug }}" hidden>
                </div>
                <div class="post-favourite-icon">
                    @if (Auth::guard('user')->user())
                        @if (App\Models\Favourite::where('blog_id', $blog->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                            <i style="color: #ae00ff; background: #ae00ff52" onclick="addFavourite({{ $blog->id }})" class="fa-solid fa-heart commonFavouriteColor{{ $blog->id }}"></i>
                        @else
                            <i onclick="addFavourite({{ $blog->id }})" class="fa-solid fa-heart commonFavouriteColor{{ $blog->id }}"></i>
                        @endif
                    @else
                        <a href="{{ route('login') }}">
                            <i class="fa-solid fa-heart"></i>
                        </a>
                    @endif
                    <span>Favourite</span>
                </div>
            </div>

            <div style="display: none" class="comment-container blog-comment-container">
                <div class="commetn-input-box">
                    <form>
                        <textarea class="comment_text{{ $blog->id }}" name="comment_text" placeholder="Write Comment...."></textarea>
                        @if (Auth::guard('user')->user())
                            <button type="button" onclick="sendComment({{ $blog->id }}, {{ $blog->rel_to_user->id }})">
                                <img src="{{ asset('assets/send.png') }}" alt="">
                            </button>
                        @else
                            <a href="{{ route('login') }}">
                                <button type="button" onclick="sendComment({{ $blog->id }})">
                                    <img src="{{ asset('assets/send.png') }}" alt="">
                                </button>
                            </a>
                        @endif
                    </form>
                </div>
                <ul class="comments-list sm-padding-left" id="comments-list{{ $blog->id }}">
                    @foreach (App\Models\Comment::where('blog_id', $blog->id)->latest()->get() as $comment)
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
                                        <i class="fa fa-reply" onclick="sendReply({{ $blog->id }},{{ $comment->id }}, {{ $comment->rel_to_user->id }})"></i>
                                        <i onclick="redirectToChatPage('{{ $comment->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                    </div>
                                    <div class="comment-content">
                                        {{ $comment->comment }}
                                    </div>
                                </div>
                            </div>

                            <ul class="comments-list reply-list reply-list{{ $comment->id }}">
                                @foreach (App\Models\Reply::where('blog_id', $blog->id)->where('comment_id', $comment->id)->where('status', 10)->latest()->get() as $reply10)
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
                                                    <i class="fa fa-reply" onclick="sendReply20({{ $blog->id }},{{ $comment->id }}, {{ $reply10->id }}, {{ $reply10->rel_to_user->id }})"></i>
                                                    <i onclick="redirectToChatPage('{{ $reply10->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                </div>
                                                <div class="comment-content">
                                                    {{ $reply10->reply }}
                                                </div>
                                            </div>
                                        </div>

                                        <ul class="comments-list reply-list reply-list10{{ $reply10->id }}">
                                            @foreach (App\Models\Reply_20::where('blog_id', $blog->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->latest()->get() as $reply20)
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
                                                                <i class="fa fa-reply" onclick="sendReply30({{ $blog->id }},{{ $comment->id }}, {{ $reply20->id }}, {{ $reply10->id }}, {{ $reply20->rel_to_user->id }})"></i>
                                                                <i onclick="redirectToChatPage('{{ $reply20->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                            </div>
                                                            <div class="comment-content">
                                                                {{ $reply20->reply }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <ul class="comments-list reply-list reply-list20{{ $reply20->id }}">
                                                        @foreach (App\Models\Reply_30::where('blog_id', $blog->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->where('reply20', $reply20->id)->latest()->get() as $reply30)
                                                            <li id="reply30_item{{ $reply30->id }}">
                                                                <div class="comment-main-level">
                                                                    <!-- Avatar -->
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
                                                                    <!-- Contenedor del Comentario -->
                                                                    <div class="comment-box">
                                                                        <div class="comment-head">
                                                                            <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">{{ $reply30->rel_to_user->fname }} {{ $reply30->rel_to_user->lname }}</a></h6>
                                                                            <span>{{ $reply30->created_at->diffForHumans() }}</span>
                                                                            {{-- <i class="fa fa-reply"></i> --}}
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
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')

    <div class="recent-blog-main">
        <h5>Related</h5>
        <div class="recent-blog-slider-list">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach ($related_blogs as $related_blog)
                        <div class="swiper-slide">
                            <a href="{{ route('read.blog', $related_blog->slug) }}">
                                <div>
                                    <img src="{{ asset('upload/blogs') }}/{{ $related_blog->blog_banner }}" alt="">
                                </div>
                                <h6>{{ Str::limit($related_blog->blog_title, 70) }}</h6>
                                <h5>{{ $related_blog->created_at->diffForHumans() }}</h5>
                            </a>
                        </div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
@endsection


@section('footer_script')
    <script>
        function blogCommentBox() {
            $('.blog-comment-container').fadeToggle(500, 'swing');
        }

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

        function commmentStyle() {
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

        commmentStyle()
    </script>

    <script>
        function see_likes() {
            const closeModalBtn = document.getElementById('closeModalBtn2');
            const modal = document.querySelector('.modal2');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);

            closeModalBtn.addEventListener('click', () => {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            });
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModalBtn.click();
                }
            });
        }
    </script>

    <script>
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

        function liked(blogID, blog_user_id) {
            const container = document.querySelector('.commonLikeColor'+blogID);
            particle(container, 'like-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/blog/like',
                type: 'POST',
                data: {
                    blog_id: blogID,
                    blog_user_id: blog_user_id,
                },
                success: function(response) {
                    if(response == 'like') {
                        $('.commonLikeColor'+blogID).css('color', '#13fdc3')
                        $('.commonLikeColor'+blogID).css('background', '#05f8af4d')
                    }else {
                        $('.commonLikeColor'+blogID).css('color', 'white')
                        $('.commonLikeColor'+blogID).css('background', 'transparent')
                    }
                }
            });
        }
    </script>

    <script>
        async function copyLinkFunc() {
            const copyInputLink = document.querySelector('.copyInput').value
            const container = document.querySelector('.commonCopyColor');
            particle(container, 'link-particle')

            try {
                await navigator.clipboard.writeText(copyInputLink);
                toastify("Link copied to clipboard!", "green")
            } catch (err) {
                toastify("Failed to copy the link.", "red")
            }
        }
    </script>

    <script>
        function addFavourite(blog_id) {
            const container = document.querySelector('.commonFavouriteColor'+blog_id);
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
                    type: 'blog',
                    blog_id: blog_id,
                },
                success: function(response) {
                    if(response == 'favourite') {
                        $('.commonFavouriteColor'+blog_id).css('color', '#ae00ff')
                        $('.commonFavouriteColor'+blog_id).css('background', '#ae00ff52')
                    }else {
                        $('.commonFavouriteColor'+blog_id).css('color', 'white')
                        $('.commonFavouriteColor'+blog_id).css('background', 'transparent')
                    }
                }
            });
        }
    </script>

    <script>
        $(".comment").click(function() {
            var postId = $(this).attr('post-id');
            const container = document.querySelector('.commentCommonColor'+postId);
            particle(container, 'comment-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/update/comment/status',
                data: {'post_id': postId},
                success: function(data) {
                    $('.comment-container'+postId).fadeToggle(500, 'swing');

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
            })
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
                                    <i onclick="redirectToChatPage('{{ Auth::guard('user')->user()->slug }}')" class="fa-solid fa-comment-dots"></i>
                                </div>
                                <div class="comment-content">
                                    ${response}
                                </div>
                            </div>
                        </div>
                    </li>
                @endif`
        }

        function replyFunction(reply_main, click, blog_id = null, comment_id = null, blog_user_id = null, reply10_id = null, reply20_id = null, value_id = null) {
            if (reply_main.next('.reply-input-box').length === 0) {
                const replyInputBox = `
                    <div class="reply-input-box">
                        <form>
                            <textarea class="comment_text reply_text${value_id}" name="reply_text" placeholder="Reply...."></textarea>
                            @if (Auth::guard('user')->user())
                                <button type="button" onclick="${click}(${blog_id}, ${comment_id}, ${blog_user_id}, ${reply10_id}, ${reply20_id})">
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
            commmentStyle();
        }

        function sendComment(blogId, blogUserId) {
            const commentText = $('.comment_text'+blogId).val();

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
                url: '/blog/comment/store',
                type: 'POST',
                data: {
                    blog_id: blogId,
                    blog_user_id: blogUserId,
                    comment_text: commentText,
                },
                success: function(response) {
                    const comment = defaultComment(response)
                    $('.comment_text'+blogId).val('');
                    $('#comments-list'+blogId).prepend(comment);
                },
            });
        }
    </script>

    <script>
        function sendReply(blog_id, comment_id, blog_user_id) {
            const commentDiv = $(".comment_item" + comment_id + " > div")
            replyFunction(commentDiv, 'sendReplyStore', blog_id, comment_id, blog_user_id, null, null, comment_id);
        }

        function sendReplyStore(blog_id, comment_id, blog_user_id) {
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
                url: '/blog/reply/store',
                type: 'POST',
                data: {
                    blog_id: blog_id,
                    comment_id: comment_id,
                    blog_user_id: blog_user_id,
                    reply_text: replyText,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+comment_id).val('')
                    $('.reply-list'+comment_id).prepend(replyNow);
                    commmentStyle()
                },
            });
        }
    </script>

    <script>
        function sendReply20(blog_id, comment_id, reply10_id, blog_user_id) {
            const replyItemList = $("#reply10_item"+reply10_id+" > div");
            replyFunction(replyItemList, 'sendReply20Store', blog_id, comment_id, blog_user_id, reply10_id, null, reply10_id);
        }

        function sendReply20Store(blog_id, comment_id, blog_user_id, reply10_id) {
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
                url: '/blog/reply/store',
                type: 'POST',
                data: {
                    blog_id: blog_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    blog_user_id: blog_user_id,
                    value: 10,
                    reply_text: reply20Text,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+reply10_id).val('')
                    $('.reply-list10'+reply10_id).prepend(replyNow);
                    commmentStyle()
                },
            });
        }
    </script>

    <script>
        function sendReply30(blog_id, comment_id, reply20_id, reply10_id, blog_user_id) {
            const reply30ItemList = $("#reply20_item"+reply20_id+" > div");
            replyFunction(reply30ItemList, 'sendReply30Store', blog_id, comment_id, blog_user_id, reply10_id, reply20_id, reply20_id);
        }

        function sendReply30Store(blog_id, comment_id, blog_user_id, reply10_id, reply20_id) {
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
                url: '/blog/reply/store',
                type: 'POST',
                data: {
                    blog_id: blog_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    blog_user_id: blog_user_id,
                    value: 20,
                    reply20_id: reply20_id,
                    reply_text: reply30Text,
                },
                success: function(response) {
                    const replyNow = defaultComment(response)
                    $('.reply_text'+reply20_id).val('')
                    $('.reply-list20'+reply20_id).prepend(replyNow);
                    commmentStyle()
                },
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
                        $('.commonFollowFollowingStatus'+user_id).text('@lang('messages.following')');
                    }else {
                        $('.commonFollowFollowingStatus'+user_id).text('@lang('messages.follow')');
                    }
                }
            });
        }
    </script>

    <script>
        function redirectToChatPage(userSlug) {
            window.location.href = `/message?user=${userSlug}`;
        }
    </script>
@endsection
