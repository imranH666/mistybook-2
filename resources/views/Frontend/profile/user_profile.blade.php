@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class='prfile'>
                <div class="card">
                    <div class="box">
                      <div class="cover-photo">
                        <img id="coverProview" src="{{ asset('upload/covers') }}/{{ $user->cover_photo == null? 'default_cover.jpg' : $user->cover_photo }}" alt="" />
                      </div>
                    </div>
                    <div class="box">
                      <div class="mt-3 content">
                        <div class="text">
                          <h2>{{ $user->fname }} {{ $user->lname }}</h2>
                          <h6>{{ $user->profession }}</h6>
                          <p class="text-center">{{ $user->description }}</p>
                        </div>
                        <ul>
                          <li>Posts <span>{{ $postCount2 }}</span></li>
                          <li>Followers <span>{{ $followerCount2 }}</span></li>
                          <li>Following <span>{{ $followingCount2 }}</span></li>
                        </ul>
                        @if (Auth::guard('user')->check())
                            @if (App\Models\Following_Follower::where('follower', $user->id)->where('following', Auth::guard('user')->user()->id)->first())
                            <button onclick="redirectToChatPage('{{ $user->slug }}')">
                                    @lang('messages.chat')
                                </button>
                            @else
                                <button class="commonFollowFollowingStatus" onclick="followingFunc({{ $user->id }})">
                                    @lang('messages.follow')
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}"><button>@lang('messages.follow')</button></a>
                        @endif

                      </div>
                    </div>
                    <div class="circle">
                        <div class="imgBox">
                            @if ($user->photo == null)
                                <img id="blah1" class="user-profile" src="{{ Avatar::create($user->fname.' '.$user->lname)->toBase64() }}" />
                            @else
                                @if (filter_var($user->photo, FILTER_VALIDATE_URL))
                                    <img id="blah2" class="user-profile" src="{{ $user->photo }}" alt="" />
                                @else
                                    <img id="blah2" class="user-profile" src="{{ asset('upload/users') }}/{{ $user->photo }}" alt="" />
                                @endif
                            @endif
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
        function followingFunc(user_id) {
            const container = document.querySelector('.commonFollowFollowingStatus');
            for (let i = 0; i < 80; i++) {
                const particle = document.createElement('div');
                particle.classList.add('follow-following-particle');
                container.appendChild(particle);

                // Generate random angles for spreading
                const angle = Math.random() * Math.PI * 2; // Random angle in radians
                const distance = Math.random() * 100 + 50; // Random distance
                const xOffset = Math.cos(angle) * distance; // X spread
                const yOffset = Math.sin(angle) * distance; // Y spread (going upward)

                // Timeline for better control
                const tl = gsap.timeline({
                    onComplete: () => {
                        // Remove particle after animation completes
                        if (container.contains(particle)) {
                            container.removeChild(particle);
                        }
                    }
                });

                // Start from the bottom center and move upwards
                tl.fromTo(
                    particle, {
                        x: 0,
                        y: 0, // Start at the bottom
                        opacity: 1,
                        scale: 0.5,
                    },
                    {
                        x: xOffset,
                        y: -yOffset, // Move upwards
                        opacity: 1,
                        scale: 1,
                        duration: 1, // Spreading duration
                        ease: 'power2.out', // Smooth spreading
                    }
                ).to(
                    particle, {
                        opacity: 0,
                        scale: 0, // Fade out and shrink
                        duration: 0.5, // Duration of fade out
                        ease: 'power1.in', // Smooth disappearing
                    }
                );
            }

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
                    if (response == 'following') {
                        $('.commonFollowFollowingStatus').text('@lang('messages.following')');
                    } else {
                        $('.commonFollowFollowingStatus').text('@lang('messages.follow')');
                    }
                }
            });
        }

        function redirectToChatPage(userSlug) {
            window.location.href = `/message?user=${userSlug}`;
        }

    </script>
@endsection
