@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="favourite-head">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">@lang('messages.follower') ({{ $followers->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">@lang('messages.following2') ({{ $followings->count() }})</button>
                    </li>
                  </ul>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="row">
                        @forelse ($followers as $follower)
                            <div class="col-lg-3 col-6 col-sm-4">
                                <div class="friend-follow">
                                    <a href="{{ route('user.profile', $follower->slug) }}">
                                        @if ($follower->photo == null)
                                        <img src="{{ Avatar::create($follower->fname.' '.$follower->lname)->toBase64() }}" />
                                        @else
                                            @if (filter_var($follower->photo, FILTER_VALIDATE_URL))
                                                <img src="{{ $follower->photo }}" alt="" />
                                            @else
                                                <img src="{{ asset('upload/users') }}/{{ $follower->photo }}" alt="" />
                                            @endif
                                        @endif
                                        <h5>{{ Str::limit($follower->fname.' '.$follower->lname, 10) }}</h5>
                                        <h6>Followrs: {{ $follower->rel_to_follower->count() }}</h6>
                                    </a>
                                    <div class="friend-follow-btn-box">
                                        <button onclick="followingFunc({{ $follower->id }})" class="friend-follow-btn-bg">
                                            @if (App\Models\Following_Follower::where('following', Auth::guard('user')->user()->id)->where('follower', $follower->id)->first())
                                                <span class="commonFollowFollowingStatus{{ $follower->id }}">@lang('messages.following')</span>
                                            @else
                                                <span class="commonFollowFollowingStatus{{ $follower->id }}">@lang('messages.follow')</span>
                                            @endif

                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-friends-box">
                                <img src="{{ asset('assets/sad.png') }}" alt="">
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="tab-pane fade favourite-question-list-container" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    <div class="row">
                        @forelse ($followings as $following)
                            <div class="col-lg-3 col-6 col-sm-4">
                                <div class="friend-follow">
                                    <a href="{{ route('user.profile', $following->slug) }}">
                                        @if ($following->photo == null)
                                            <img src="{{ Avatar::create($following->fname.' '.$following->lname)->toBase64() }}" />
                                        @else
                                            @if (filter_var($following->photo, FILTER_VALIDATE_URL))
                                                <img src="{{ $following->photo }}" alt="" />
                                            @else
                                                <img src="{{ asset('upload/users') }}/{{ $following->photo }}" alt="" />
                                            @endif
                                        @endif
                                        <h5>{{ Str::limit($following->fname.' '.$following->lname, 10) }}</h5>
                                        <h6>Followrs: {{ $following->rel_to_follower->count() }}</h6>
                                    </a>
                                    <div class="friend-follow-btn-box">
                                        <a href="{{ route('user.profile', $following->slug) }}">
                                            <button class="friend-follow-btn-bg"><span>@lang('messages.see')</span></button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-friends-box">
                                <img src="{{ asset('assets/sad.png') }}" alt="">
                            </div>
                        @endforelse
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
