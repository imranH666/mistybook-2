@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-6 col-sm-4">
            <div class="friend-follow-container">
                <div class="row">
                    @foreach ($users as $user)
                        <div class="col-lg-6 col-6 col-sm-12">
                            <div class="friend-follow">
                                <a href="{{ route('user.profile', $user->slug) }}">
                                    @if ($user->photo == null)
                                        <img src="{{ Avatar::create($user->fname.' '.$user->lname)->toBase64() }}" />
                                    @else
                                        @if (filter_var($user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $user->photo }}" alt="" />
                                        @else
                                            <img src="{{ asset('upload/users') }}/{{ $user->photo }}" alt="" />
                                        @endif
                                    @endif
                                    <h5>{{ Str::limit($user->fname.' '.$user->lname, 10) }}</h5>
                                    <h6>Followrs: {{ $user->rel_to_follower->count() }}</h6>
                                </a>
                                <div class="friend-follow-btn-box">
                                    <button onclick="followingFunc({{ $user->id }})" class="friend-follow-btn-bg"><span class="commonFollowFollowingStatus{{ $user->id }}">@lang('messages.follow')</span></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-8">
            <div class="notification-container">
                @forelse (App\Models\Notification::where('to_notification', Auth::guard('user')->user()->id)->latest()->get() as $notification)
                    @if ($notification->status == 1)
                        <a href="{{ route('user.profile', ['slug' => $notification->link, 'id' => $notification->id]) }}">
                            <div class="notification {{ $notification->see == 0 ? 'active' : '' }}">
                                <div class="notification-profile">
                                    @if ($notification->rel_to_user->photo == null)
                                        <img src="{{ Avatar::create($notification->rel_to_user->fname . ' ' . $notification->rel_to_user->lname)->toBase64() }}" alt="">
                                    @else
                                        @if (filter_var($notification->rel_to_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $notification->rel_to_user->photo }}" alt="">
                                        @else
                                            <img src="{{ asset('upload/users/' . $notification->rel_to_user->photo) }}" alt="">
                                        @endif
                                    @endif
                                </div>
                                <div class="notification-text">
                                    <h5> {{ $notification->rel_to_user->fname }} {{ $notification->rel_to_user->lname }} <span>{!! Str::limit($notification->message, 80, '...') !!}</span></h5>
                                    <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                    <a class="notification-delete-btn" href="{{ route('notification.delete', $notification->id) }}"><i class="fa-solid fa-circle-xmark"></i></a>
                                </div>
                            </div>
                        </a>
                    @elseif ($notification->status == 2)
                        <a href="{{ route('read.blog', ['slug' => $notification->link, 'id' => $notification->id]) }}">
                            <div class="notification {{ $notification->see == 0 ? 'active' : '' }}">
                                <div class="notification-profile">
                                    @if ($notification->rel_to_user->photo == null)
                                        <img src="{{ Avatar::create($notification->rel_to_user->fname . ' ' . $notification->rel_to_user->lname)->toBase64() }}" alt="">
                                    @else
                                        @if (filter_var($notification->rel_to_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $notification->rel_to_user->photo }}" alt="">
                                        @else
                                            <img src="{{ asset('upload/users/' . $notification->rel_to_user->photo) }}" alt="">
                                        @endif
                                    @endif
                                </div>
                                <div class="notification-text">
                                    <h5> {{ $notification->rel_to_user->fname }} {{ $notification->rel_to_user->lname }} <span>{!! Str::limit($notification->message, 80, '...') !!}</span></h5>
                                    <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                    <a class="notification-delete-btn" href="{{ route('notification.delete', $notification->id) }}"><i class="fa-solid fa-circle-xmark"></i></a>
                                </div>
                            </div>
                        </a>
                    @elseif ($notification->status == 3)
                        <a href="{{ route('see.video', ['slug' => $notification->link, 'id' => $notification->id]) }}">
                            <div class="notification {{ $notification->see == 0 ? 'active' : '' }}">
                                <div class="notification-profile">
                                    @if ($notification->rel_to_user->photo == null)
                                        <img src="{{ Avatar::create($notification->rel_to_user->fname . ' ' . $notification->rel_to_user->lname)->toBase64() }}" alt="">
                                    @else
                                        @if (filter_var($notification->rel_to_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $notification->rel_to_user->photo }}" alt="">
                                        @else
                                            <img src="{{ asset('upload/users/' . $notification->rel_to_user->photo) }}" alt="">
                                        @endif
                                    @endif
                                </div>
                                <div class="notification-text">
                                    <h5> {{ $notification->rel_to_user->fname }} {{ $notification->rel_to_user->lname }} <span>{!! Str::limit($notification->message, 80, '...') !!}</span></h5>
                                    <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                    <a class="notification-delete-btn" href="{{ route('notification.delete', $notification->id) }}"><i class="fa-solid fa-circle-xmark"></i></a>
                                </div>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('show.post', ['slug' => $notification->link, 'id' => $notification->id]) }}">
                            <div class="notification {{ $notification->see == 0 ? 'active' : '' }}">
                                <div class="notification-profile">
                                    @if ($notification->rel_to_user->photo == null)
                                        <img src="{{ Avatar::create($notification->rel_to_user->fname . ' ' . $notification->rel_to_user->lname)->toBase64() }}" alt="">
                                    @else
                                        @if (filter_var($notification->rel_to_user->photo, FILTER_VALIDATE_URL))
                                            <img src="{{ $notification->rel_to_user->photo }}" alt="">
                                        @else
                                            <img src="{{ asset('upload/users/' . $notification->rel_to_user->photo) }}" alt="">
                                        @endif
                                    @endif
                                </div>
                                <div class="notification-text">
                                    <h5> {{ $notification->rel_to_user->fname }} {{ $notification->rel_to_user->lname }} <span>{!! Str::limit($notification->message, 80, '...') !!}</span></h5>
                                    <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                    <a class="notification-delete-btn" href="{{ route('notification.delete', $notification->id) }}"><i class="fa-solid fa-circle-xmark"></i></a>
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <h6 class="empty-notification">Empty Notification</h6>
                @endforelse
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
