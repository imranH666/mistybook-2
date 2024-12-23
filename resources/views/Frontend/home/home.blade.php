@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="post-container">
                <div class='introBar'>
                    <div class="post" onclick="window.location.href = '{{ route('blog') }}'">
                        @if (Auth::guard('user')->user())
                            <a href="{{ route('profile') }}">
                                @if (Auth::guard('user')->user()->photo == null)
                                    <img class="user-profile" src="{{ Avatar::create(Auth::guard('user')->user()->fname.' '.Auth::guard('user')->user()->lname)->toBase64() }}" />
                                @else
                                    @if (filter_var(Auth::guard('user')->user()->photo, FILTER_VALIDATE_URL))
                                        <img class="user-profile" src="{{ Auth::guard('user')->user()->photo }}" alt="Google Profile Image" />
                                    @else
                                        <img class="user-profile" src="{{ asset('upload/users') }}/{{ Auth::guard('user')->user()->photo }}" alt="Local Profile Image" />
                                    @endif
                                @endif
                            </a>
                        @endif
                        <p>@lang('messages.do-you-want-to-write')</p>
                    </div>
                    <div class="story" onclick="window.location.href = '{{ route('create.post') }}'">
                        <i class="fa-solid fa-film"></i>
                        <h6>@lang('messages.createPost')</h6>
                    </div>
                </div>

                @php
                    $randomInterval = rand(3, 5);
                @endphp

                @foreach ($posts as $index => $post)
                    <div id="modal" class="modal modal{{ $post->id }}">
                        <div class="modal-content for-post">
                            <i id="closeModalBtn{{ $post->id }}" class="fa-solid fa-square-xmark question-close-btn ttttt{{ $post->id }}"></i>
                            <h6>Please write the reason if you'd like to report this.</h6>
                            <form>
                                @csrf
                                <textarea class="report_text{{ $post->id }}" name="report_text"></textarea>
                                <div class="my-2 text-center">
                                    @if (Auth::guard('user')->user())
                                        <button onclick="reportPost({{ $post->id }})" type="button">Report</button>
                                        <p class="report-message{{ $post->id }}"></p>
                                    @else
                                        <a href="{{ route('login') }}"><button type="button">Report</button></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal modal2{{ $post->id }}">
                        <div class="modal-content for-likes">
                            <i id="closeModalBtn2{{ $post->id }}" class="fa-solid fa-square-xmark question-close-btn"></i>
                            <ul class="post-likers">
                                @forelse (App\Models\Like::where('post_id', $post->id)->latest()->get() as $like_user)
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
                                            @if (Auth::guard('user')->user())
                                                @if (App\Models\Following_Follower::where('follower', $like_user->rel_to_like_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                                    @lang('messages.following')
                                                @else
                                                    @lang('messages.follow')
                                                @endif
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

                    <div class='post-main post-main{{ $post->id }}'>
                        <div class="post-head">
                            <a href="{{ route('user.profile', $post->rel_to_user->slug) }}">
                                @if ($post->rel_to_user->photo == null)
                                    <img id="blah" class="user-profile" src="{{ Avatar::create($post->rel_to_user->fname.' '.$post->rel_to_user->lname)->toBase64() }}" />
                                @else
                                    @if (filter_var($post->rel_to_user->photo, FILTER_VALIDATE_URL))
                                        <img src="{{ $post->rel_to_user->photo }}" alt="" />
                                    @else
                                        <img src="{{ asset('upload/users') }}/{{ $post->rel_to_user->photo }}" alt="" />
                                    @endif
                                @endif
                            </a>
                            <div class="post-name-time">
                                <h4><a href="{{ route('user.profile', $post->rel_to_user->slug) }}">{{ $post->rel_to_user->fname }} {{ $post->rel_to_user->lname }}</a>
                                    @if (Auth::guard('user')->user())
                                        <span class="commonFollowFollowingStatus{{ $post->rel_to_user->id }}" onclick="followingFunc({{ $post->rel_to_user->id }})">
                                            @if (App\Models\Following_Follower::where('follower', $post->rel_to_user->id)->where('following', Auth::guard('user')->user()->id)->first())
                                                @lang('messages.following')
                                            @else
                                                @lang('messages.follow')
                                            @endif
                                        </span>
                                    @else
                                        <a href="{{ route('login') }}"><span> @lang('messages.follow')</span></a>
                                    @endif
                                </h4>
                                <h6>{{ $post->created_at->diffForHumans() }}</h6>
                            </div>
                            <div class="post-three-dot-menu-btn">
                                <i onclick="visibleThreeDotBox({{ $post->id }})" class="fa-solid fa-ellipsis-vertical"></i>
                                <div class="post-three-dot-menu-action-box post-three-dot-menu-action-box{{ $post->id }}">
                                    @if (Auth::guard('user')->user())
                                        <button onclick="hidePost({{ $post->id }})">Hide</button>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <button type="button">Hide</button>
                                        </a>
                                    @endif
                                    <button onclick="copyLinkFunc({{ $post->id }})">Copy Link</button>
                                    @if (Auth::guard('user')->user())
                                        <button onclick="report({{ $post->id }})">Report</button>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <button type="button">Report</button>
                                        </a>
                                    @endif
                                </div>
                                <input type="text" class="copyInput{{ $post->id }}" value="{{ config('app.url') }}/post/{{ $post->slug }}" hidden>
                            </div>
                        </div>
                        <div class="{{ $post->share == 1? 'share-div' : '' }}">
                            @if ($post->share == 1)
                                <div class="post-head">
                                    <a href="{{ route('user.profile', $post->rel_to_share->slug) }}">
                                        @if ($post->rel_to_share->photo == null)
                                            <img style="margin-bottom: 10px" id="blah" class="user-profile" src="{{ Avatar::create($post->rel_to_share->fname.' '.$post->rel_to_share->lname)->toBase64() }}" />
                                        @else
                                            @if (filter_var($post->rel_to_share->photo, FILTER_VALIDATE_URL))
                                                <img style="margin-bottom: 10px" src="{{ $post->rel_to_share->photo }}" alt="" />
                                            @else
                                                <img style="margin-bottom: 10px" src="{{ asset('upload/users') }}/{{ $post->rel_to_share->photo }}" alt="" />
                                            @endif
                                        @endif
                                    </a>
                                    <div class="post-name-time">
                                        <h4><a href="{{ route('user.profile', $post->rel_to_share->slug) }}">{{ $post->rel_to_share->fname }} {{ $post->rel_to_share->lname }}</a>
                                            @if (Auth::guard('user')->user())
                                                <span class="commonFollowFollowingStatus{{ $post->rel_to_share->id }}" onclick="followingFunc({{ $post->rel_to_share->id }})">
                                                    @if (App\Models\Following_Follower::where('follower', $post->rel_to_share->id)->where('following', Auth::guard('user')->user()->id)->first())
                                                        @lang('messages.following')
                                                    @else
                                                        @lang('messages.follow')
                                                    @endif
                                                </span>
                                            @else
                                                <a href="{{ route('login') }}"><span> @lang('messages.follow')</span></a>
                                            @endif
                                        </h4>
                                    </div>
                                </div>
                            @endif
                            <div class="post-content">
                                @php
                                    $isLongContent = strlen($post->content) > 600;
                                @endphp

                                @if ($isLongContent)
                                    <article class="short-content{{ $post->id }}">
                                        {!! Str::limit($post->content, 600) !!}
                                        <button class="see-less-btn" onclick="moreContent({{ $post->id }}, 'short')"> @lang('messages.see-more')</button>
                                    </article>
                                    <article style="display: none" class="full-content{{ $post->id }}">
                                        {!! $post->content !!}
                                        <button class="see-less-btn" onclick="moreContent({{ $post->id }}, 'full')"> @lang('messages.see-less')</button>
                                    </article>
                                @else
                                    <article>
                                        {!! $post->content !!}
                                    </article>
                                @endif
                            </div>
                            <div class="post-img">
                                <div class="row g-1">
                                    @php
                                        $imageCount = $post->rel_to_post_images->count();
                                    @endphp
                                    @foreach ($post->rel_to_post_images as $post_image)
                                        <div class="col-lg-{{ $imageCount == 1 ? '12' : '6' }} col-{{ $imageCount == 1 ? '12' : '6' }} col-sm-{{ $imageCount == 1 ? '12' : '6' }} col-md-{{ $imageCount == 1 ? '12' : '6' }}">
                                            <a href="{{ asset('upload/posts') }}/{{ $post_image->image_path }}" data-lightbox="image-{{ $post->id }}"><img src="" data-src="{{ asset('upload/posts') }}/{{ $post_image->image_path }}" alt="Loading" class="lazy"/></a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="post-actions">
                                <div class="like-comment">
                                    <div>
                                        @if (Auth::guard('user')->user())
                                            @if (App\Models\Like::where('user_id', Auth::guard('user')->user()->id)->where('post_id', $post->id)->first())
                                                <i class="fa-solid fa-thumbs-up commonLikeColor{{ $post->id }} like-active" onclick="liked({{ $post->id }}, {{ $post->rel_to_user->id }})"></i>
                                            @else
                                                <i class="fa-solid fa-thumbs-up commonLikeColor{{ $post->id }}" onclick="liked({{ $post->id }}, {{ $post->rel_to_user->id }})"></i>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}"><i class="fa-solid fa-thumbs-up commonLikeColor"></i></a>
                                        @endif
                                        <span style="cursor: pointer" onclick="see_likes({{ $post->id }})">Liked ({{ $post->rel_to_like_count->count() }})</span>
                                        <span style="cursor: pointer" onclick="see_likes({{ $post->id }})" class="sm-like-count">({{ $post->rel_to_like_count->count() }})</span>
                                    </div>
                                    <div class="comment" id="comment" post-id="{{ $post->id }}">
                                        @if (Auth::guard('user')->user())
                                            @if (App\Models\Comment::where('post_id', $post->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                                                <i class="fa-solid fa-comment comment-active commentCommonColor{{ $post->id }}"></i>
                                            @else
                                                <i class="fa-solid fa-comment commentCommonColor{{ $post->id }}"></i>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}"><i class="fa-solid fa-comment commentCommonColor{{ $post->id }}"></i></a>
                                        @endif
                                        <span>Comments ({{ $post->rel_to_post_comments->count() + $post->rel_to_post_reply->count() + $post->rel_to_post_reply_20->count() + $post->rel_to_post_reply_30->count() }})</span>
                                        <span class="sm-comment-count">({{ $post->rel_to_post_comments->count() + $post->rel_to_post_reply->count() + $post->rel_to_post_reply_20->count() + $post->rel_to_post_reply_30->count() }})</span>
                                    </div>
                                </div>
                                <div class="share">
                                @if (Auth::guard('user')->user())
                                    @if (App\Models\Share::where('post_id', $post->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                                        <i class="fa-solid fa-share commonShareColor{{ $post->id }} share-active" onclick="sharePost({{ $post->id }})"></i>
                                    @else
                                        <i class="fa-solid fa-share commonShareColor{{ $post->id }}" onclick="sharePost({{ $post->id }})"></i>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"><i class="fa-solid fa-share }}"></i></a>
                                @endif
                                    <span>Share ({{ $post->rel_to_share_count->count() }})</span>
                                    <span class="sm-share-count">({{ $post->rel_to_share_count->count() }})</span>
                                </div>
                                <div class="post-favourite-icon">
                                    @if (Auth::guard('user')->user())
                                        @if (App\Models\Favourite::where('post_id', $post->id)->where('user_id', Auth::guard('user')->user()->id)->first())
                                            <i style="color: #ae00ff; background: #ae00ff52" onclick="addFavourite({{ $post->id }})" class="fa-solid fa-heart commonFavouriteColor{{ $post->id }}"></i>
                                        @else
                                            <i onclick="addFavourite({{ $post->id }})" class="fa-solid fa-heart commonFavouriteColor{{ $post->id }}"></i>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}">
                                            <i class="fa-solid fa-heart"></i>
                                        </a>
                                    @endif
                                    <span>Favourite</span>
                                </div>
                            </div>
                        </div>

                        <div class="comment-container comment-container{{ $post->id }}">
                            <div class="commetn-input-box">
                                <form>
                                    <textarea class="comment_text{{ $post->id }}" name="comment_text" placeholder="Write Comment...."></textarea>
                                    @if (Auth::guard('user')->user())
                                        <button type="button" onclick="sendComment({{ $post->id }}, {{ $post->rel_to_user->id }})">
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

                            <ul id="comments-list{{ $post->id }}" class="comments-list sm-padding-left" >
                                @foreach (App\Models\Comment::where('post_id', $post->id)->latest()->get() as $comment)
                                    <li class="comment_item{{ $comment->id }}">
                                        <div class="comment-main-level">
                                            <div class="comment-avatar">
                                                @if ($comment->rel_to_user->photo == null)
                                                    <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">
                                                        <img id="blah" class="user-profile" src="{{ Avatar::create($comment->rel_to_user->fname.' '.$comment->rel_to_user->lname)->toBase64() }}" />
                                                    </a>
                                                @else
                                                    @if (filter_var($comment->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                        <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">
                                                            <img src="{{ $comment->rel_to_user->photo }}" alt="">
                                                        </a>
                                                    @else
                                                        <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">
                                                            <img src="{{ asset('upload/users') }}/{{ $comment->rel_to_user->photo }}" alt="">
                                                        </a>
                                                    @endif
                                                @endif

                                            </div>
                                            <div class="comment-box">
                                                <div class="comment-head">
                                                    <h6 class="comment-name">
                                                        <a href="{{ route('user.profile', $comment->rel_to_user->slug) }}">{{ $comment->rel_to_user->fname }} {{ $comment->rel_to_user->lname }}
                                                        </a>
                                                    </h6>
                                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                                    <i class="fa fa-reply" onclick="sendReply({{ $post->id }},{{ $comment->id }}, {{ $comment->rel_to_user->id }})"></i>
                                                    <i onclick="redirectToChatPage('{{ $comment->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                </div>
                                                <div class="comment-content">
                                                    {{ $comment->comment }}
                                                </div>
                                            </div>
                                        </div>

                                        <ul class="comments-list reply-list reply-list{{ $comment->id }}">
                                            @foreach (App\Models\Reply::where('post_id', $post->id)->where('comment_id', $comment->id)->where('status', 10)->latest()->get() as $reply10)
                                                <li id="reply10_item{{ $reply10->id }}">
                                                    <div class="comment-main-level">
                                                        <!-- Avatar -->
                                                        <div class="comment-avatar reply-avatar">
                                                            @if ($reply10->rel_to_user->photo == null)
                                                               <a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">
                                                                    <img id="blah" class="user-profile" src="{{ Avatar::create($reply10->rel_to_user->fname.' '.$reply10->rel_to_user->lname)->toBase64() }}" />
                                                               </a>
                                                            @else
                                                                @if (filter_var($reply10->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                    <a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">
                                                                        <img src="{{ $reply10->rel_to_user->photo }}" alt="">
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">
                                                                        <img src="{{ asset('upload/users') }}/{{ $reply10->rel_to_user->photo }}" alt="">
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <!-- Contenedor del Comentario -->
                                                        <div class="comment-box">
                                                            <div class="comment-head">
                                                                <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply10->rel_to_user->slug) }}">{{ $reply10->rel_to_user->fname }} {{ $reply10->rel_to_user->lname }}</a></h6>
                                                                <span>{{ $reply10->created_at->diffForHumans() }}</span>
                                                                <i class="fa fa-reply" onclick="sendReply20({{ $post->id }},{{ $comment->id }}, {{ $reply10->id }}, {{ $reply10->rel_to_user->id }})"></i>
                                                                <i onclick="redirectToChatPage('{{ $reply10->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                            </div>
                                                            <div class="comment-content">
                                                                {{ $reply10->reply }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <ul class="comments-list reply-list reply-list10{{ $reply10->id }}">
                                                        @foreach (App\Models\Reply_20::where('post_id', $post->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->latest()->get() as $reply20)
                                                            <li id="reply20_item{{ $reply20->id }}">
                                                                <div class="comment-main-level">
                                                                    <!-- Avatar -->
                                                                    <div class="comment-avatar reply-avatar">
                                                                        @if ($reply20->rel_to_user->photo == null)
                                                                            <a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">
                                                                                <img id="blah" class="user-profile" src="{{ Avatar::create($reply20->rel_to_user->fname.' '.$reply20->rel_to_user->lname)->toBase64() }}" />
                                                                            </a>
                                                                        @else
                                                                            @if (filter_var($reply20->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                                <a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">
                                                                                    <img src="{{ $reply20->rel_to_user->photo }}" alt="">
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">
                                                                                    <img src="{{ asset('upload/users') }}/{{ $reply20->rel_to_user->photo }}" alt="">
                                                                                </a>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                    <!-- Contenedor del Comentario -->
                                                                    <div class="comment-box">
                                                                        <div class="comment-head">
                                                                            <h6 class="comment-name by-author"><a href="{{ route('user.profile', $reply20->rel_to_user->slug) }}">{{ $reply20->rel_to_user->fname }} {{ $reply20->rel_to_user->lname }}</a></h6>
                                                                            <span>{{ $reply20->created_at->diffForHumans() }}</span>
                                                                            <i class="fa fa-reply" onclick="sendReply30({{ $post->id }},{{ $comment->id }}, {{ $reply20->id }}, {{ $reply10->id }}, {{ $reply20->rel_to_user->id }})"></i>
                                                                            <i onclick="redirectToChatPage('{{ $reply20->rel_to_user->slug }}')" class="fa-solid fa-comment-dots"></i>
                                                                        </div>
                                                                        <div class="comment-content">
                                                                            {{ $reply20->reply }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <ul class="comments-list reply-list reply-list20{{ $reply20->id }}">
                                                                    @foreach (App\Models\Reply_30::where('post_id', $post->id)->where('comment_id', $comment->id)->where('status', 20)->where('reply10', $reply10->id)->where('reply20', $reply20->id)->latest()->get() as $reply30)
                                                                        <li id="reply30_item{{ $reply30->id }}">
                                                                            <div class="comment-main-level">
                                                                                <!-- Avatar -->
                                                                                <div class="comment-avatar reply-avatar">
                                                                                    @if ($reply30->rel_to_user->photo == null)
                                                                                        <a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">
                                                                                            <img id="blah" class="user-profile" src="{{ Avatar::create($reply30->rel_to_user->fname.' '.$reply30->rel_to_user->lname)->toBase64() }}" />
                                                                                        </a>
                                                                                    @else
                                                                                        @if (filter_var($reply30->rel_to_user->photo, FILTER_VALIDATE_URL))
                                                                                            <a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">
                                                                                                <img src="{{ $reply30->rel_to_user->photo }}" alt="">
                                                                                            </a>
                                                                                        @else
                                                                                            <a href="{{ route('user.profile', $reply30->rel_to_user->slug) }}">
                                                                                                <img src="{{ asset('upload/users') }}/{{ $reply30->rel_to_user->photo }}" alt="">
                                                                                            </a>
                                                                                        @endif
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

                    @if(($index + 1) % $randomInterval == 0)
                        @php
                            $random_num = rand(1, 3);
                        @endphp

                        @if ($random_num == 1)
                            <div class="recent-blog-main">
                                <div class="home-category-slider">
                                    <div class="category-swiper">
                                        <div class="swiper-wrapper">
                                            @foreach ($categories as $category)
                                                <div class="swiper-slide">
                                                    <a href="{{ route('see.category', app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name) }}">
                                                        <div class="image-div">
                                                            <img src="{{ asset('upload/categories') }}/{{ $category->category_image }}" alt="">
                                                            <div class="home-category-slider-content">
                                                                <h4>{{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}</h4>
                                                                <p>{{ Str::limit(app()->getLocale() == 'en' ? $category->category_english_description : $category->category_bangla_description, 150, '...') }}</p> <button>@lang('messages.read')</button>
                                                            </div>
                                                        </div>
                                                        {{-- <h6>{{ Str::limit($blog->blog_title, 50) }}</h6>
                                                        <h5>{{ $blog->created_at->diffForHumans() }}</h5>
                                                        <div class="recent-blog-post-content">
                                                            {!! Str::limit(strip_tags($blog->blog_content), 150, '...') !!}
                                                        </div> --}}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Add Pagination -->
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($random_num == 2)
                            <div class="recent-blog-main">
                                <div class="recent-home-blog-slider-list">
                                    <div class="post-swiper">
                                        <div class="swiper-wrapper">
                                            @foreach ($blogs as $blog)
                                                <div class="swiper-slide">
                                                    <a href="{{ route('read.blog', $blog->slug) }}">
                                                        <div class="image-div">
                                                            <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt="">
                                                        </div>
                                                        <h6>{{ Str::limit($blog->blog_title, 50) }}</h6>
                                                        <h5>{{ $blog->created_at->diffForHumans() }}</h5>
                                                        <div class="recent-blog-post-content">
                                                            {!! Str::limit(strip_tags($blog->blog_content), 120, '...') !!}
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Add Pagination -->
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="recent-blog-main">
                                <div class="home-friend-slider-list">
                                    <div class="friend-swiper">
                                        <div class="swiper-wrapper">
                                            @foreach ($friends as $friend)
                                                <div class="swiper-slide">
                                                    <div class="friend-follow">
                                                        <a href="{{ route('user.profile', $friend->slug) }}">
                                                            @if ($friend->photo == null)
                                                            <img src="{{ Avatar::create($friend->fname.' '.$friend->lname)->toBase64() }}" />
                                                            @else
                                                                @if (filter_var($friend->photo, FILTER_VALIDATE_URL))
                                                                    <img src="{{ $friend->photo }}" alt="" />
                                                                @else
                                                                    <img src="{{ asset('upload/users') }}/{{ $friend->photo }}" alt="" />
                                                                @endif
                                                            @endif
                                                            <h5>{{ Str::limit($friend->fname.' '.$friend->lname, 10) }}</h5>
                                                            <h6>Followrs: {{ $friend->rel_to_follower->count() }}</h6>
                                                        </a>
                                                        <div class="friend-follow-btn-box">
                                                            <button onclick="followingFunc({{ $friend->id }})" class="friend-follow-btn-bg">
                                                                @if (Auth::guard('user')->user())
                                                                    @if (App\Models\Following_Follower::where('following', Auth::guard('user')->user()->id)->where('follower', $friend->id)->first())
                                                                        <span class="commonFollowFollowingStatus{{ $friend->id }}">@lang('messages.following')</span>
                                                                    @else
                                                                        <span class="commonFollowFollowingStatus{{ $friend->id }}">@lang('messages.follow')</span>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route('login') }}"><span>@lang('messages.follow')</span></a>
                                                                @endif
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php
                            $randomInterval = rand(5, 10);
                        @endphp
                    @endif
                @endforeach
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
        new LazyLoad({
            elements_selector: ".lazy"
        });
    </script>

    <script>
        function swiperSlide(class_name, slidesPerView, ss, sm, md, lg) {
            const swiper = new Swiper(class_name, {
                loop: true,
                slidesPerView: slidesPerView,
                spaceBetween: 10,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    "@0.00": {
                        slidesPerView: ss,
                        spaceBetween: 10,
                    },
                    "@0.75": {
                        slidesPerView: sm,
                        spaceBetween: 10,
                    },
                    "@1.00": {
                        slidesPerView: md,
                        spaceBetween: 10,
                    },
                    "@1.50": {
                        slidesPerView: lg,
                        spaceBetween: 10,
                    },
                },
            });
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

        document.addEventListener('DOMContentLoaded', () => {
            swiperSlide('.post-swiper', 3, 1, 2, 3, 3)
            swiperSlide('.friend-swiper', 4, 2, 3, 4, 4)
            swiperSlide('.category-swiper', 2, 1, 1, 2, 2)
        });
    </script>

    <script>
        function visibleThreeDotBox(id) {
            $('.post-three-dot-menu-action-box'+id).fadeToggle(500, 'swing');
        }

        function hidePost(id) {
            $('.post-main'+id).css('display', 'none')
        }

        function report(id) {
            const closeModalBtn = document.getElementById('closeModalBtn'+id);
            const modal = document.querySelector('.modal'+id);
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

        function see_likes(id) {
            const closeModalBtn = document.getElementById('closeModalBtn2'+id);
            const modal = document.querySelector('.modal2'+id);
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

        async function copyLinkFunc(id) {
            const copyInputLink = document.querySelector('.copyInput'+id).value

            try {
                await navigator.clipboard.writeText(copyInputLink);
                toastify("Link copied to clipboard!", "green")
            } catch (err) {
                toastify("Failed to copy the link.", "red")
            }
        }

        function reportPost(postID) {
            const report_text = document.querySelector('.report_text'+postID).value
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/post/report',
                type: 'POST',
                data: {
                    post_id: postID,
                    report_text: report_text,
                },
                success: function(response) {
                    if(response == 'success') {
                        $('.report-message'+postID).text('Reported successfully')
                        $('.report-message'+postID).css('color', 'green')
                        document.querySelector('.report_text'+postID).value = ''
                    }else {
                        $('.report-message'+postID).text('Please write something')
                        $('.report-message'+postID).css('color', 'red')
                    }
                }
            });
        }
    </script>

    <script>
        function moreContent(post_id, value) {
            if(value === 'full') {
                $('.short-content'+post_id).css('display', 'block')
                $('.full-content'+post_id).css('display', 'none')
            }else {
                $('.short-content'+post_id).css('display', 'none')
                $('.full-content'+post_id).css('display', 'block')
            }
        }
    </script>

    <script>
        function sharePost(postID) {
            const container = document.querySelector('.commonShareColor'+postID);
            particle(container, 'share-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/post/share',
                type: 'POST',
                data: {
                    post_id: postID,
                    // post_user_id: post_user_id,
                },
                success: function(response) {
                    $('.commonShareColor'+postID).css('color', '#ff9100')
                    $('.commonShareColor'+postID).css('background', '#ff910060')
                }
            });
        }
    </script>

    <script>
        function liked(postID, post_user_id) {
            const container = document.querySelector('.commonLikeColor'+postID);
            particle(container, 'like-particle')

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/post/like',
                type: 'POST',
                data: {
                    post_id: postID,
                    post_user_id: post_user_id,
                },
                success: function(response) {
                    if(response == 'like') {
                        $('.commonLikeColor'+postID).css('color', '#09a564')
                        $('.commonLikeColor'+postID).css('background', '#05f8af3f')
                    }else {
                        $('.commonLikeColor'+postID).css('color', 'white')
                        $('.commonLikeColor'+postID).css('background', 'transparent')
                    }
                }
            });
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
                    commonStyle()
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
                                        <img id="blah" class="user-profile" src="{{ Auth::guard('user')->user()->photo }}" alt="" />
                                    @else
                                        <img id="blah" class="user-profile" src="{{ asset('upload/users') }}/{{ Auth::guard('user')->user()->photo }}" alt="" />
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

        function replyFunction(reply_main, click, post_id = null, comment_id = null, post_user_id = null, reply10_id = null, reply20_id = null, value_id = null) {
            if (reply_main.next('.reply-input-box').length === 0) {
                const replyInputBox = `
                    <div class="reply-input-box">
                        <form>
                            <textarea class="comment_text reply_text${value_id}" name="reply_text" placeholder="Reply...."></textarea>
                            @if (Auth::guard('user')->user())
                                <button type="button" onclick="${click}(${post_id}, ${comment_id}, ${post_user_id}, ${reply10_id}, ${reply20_id})">
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

        function sendComment(postId, postUserId) {
            const commentText = $('.comment_text'+postId).val();
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
                url: '/user/comment/store',
                type: 'POST',
                data: {
                    post_id: postId,
                    post_user_id: postUserId,
                    comment_text: commentText,
                },
                success: function(response) {
                    let comment = defaultComment(response)
                    $('.comment_text'+postId).val('');
                    $('#comments-list'+postId).prepend(comment);
                },
            });
        }
    </script>

    <script>
        function sendReply(post_id, comment_id, post_user_id) {
            const commentDiv = $(".comment_item" + comment_id + " > div");
            replyFunction(commentDiv, 'sendReplyStore', post_id, comment_id, post_user_id, null, null, comment_id);
        }

        function sendReplyStore(post_id, comment_id, post_user_id) {
            const replyText = $('.reply_text'+comment_id).val();
            if (replyText.trim() === "") {
                toastify("Please, reply something", "pink");
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });
            $.ajax({
                url: '/user/reply/store',
                type: 'POST',
                data: {
                    post_id: post_id,
                    comment_id: comment_id,
                    post_user_id: post_user_id,
                    reply_text: replyText,
                },
                success: function (response) {
                    const replyNow = defaultComment(response);
                    $('.reply_text'+comment_id).val('')
                    $('.reply-list' + comment_id).prepend(replyNow);
                    commonStyle();
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                    toastify("Something went wrong. Please try again.", "red");
                },
            });
        }
    </script>

    <script>
        function sendReply20(post_id, comment_id, reply10_id, post_user_id) {
            const replyItemList = $("#reply10_item"+reply10_id+" > div");
            replyFunction(replyItemList, 'sendReply20Store', post_id, comment_id, post_user_id, reply10_id, null, reply10_id);
        }

        function sendReply20Store(post_id, comment_id, post_user_id, reply10_id) {
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
                url: '/user/reply/store',
                type: 'POST',
                data: {
                    post_id: post_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    post_user_id: post_user_id,
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
        function sendReply30(post_id, comment_id, reply20_id, reply10_id, post_user_id) {
            const reply30ItemList = $("#reply20_item"+reply20_id+" > div");
            replyFunction(reply30ItemList, 'sendReply30Store', post_id, comment_id, post_user_id, reply10_id, reply20_id, reply20_id);
        }

        function sendReply30Store(post_id, comment_id, post_user_id, reply10_id, reply20_id) {
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
                url: '/user/reply/store',
                type: 'POST',
                data: {
                    post_id: post_id,
                    comment_id: comment_id,
                    reply10_id: reply10_id,
                    post_user_id: post_user_id,
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

        function addFavourite(post_id) {
            const container = document.querySelector('.commonFavouriteColor'+post_id);
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
                    type: 'post',
                    post_id: post_id,
                },
                success: function(response) {
                    if(response == 'favourite') {
                        $('.commonFavouriteColor'+post_id).css('color', '#ae00ff')
                        $('.commonFavouriteColor'+post_id).css('background', '#ae00ff52')
                    }else {
                        $('.commonFavouriteColor'+post_id).css('color', 'white')
                        $('.commonFavouriteColor'+post_id).css('background', 'transparent')
                    }
                }
            });
        }

        function redirectToChatPage(userSlug) {
            window.location.href = `/message?user=${userSlug}`;
        }
    </script>
@endsection
