@extends('Frontend.navber.navber')


@section('content')
    <div id="modal" class="modal">
        <div class="modal-content profile">
            <i id="closeModalBtn" class="fa-solid fa-square-xmark question-close-btn"></i>
            <h4>Edit Profile</h4>
            <form action="{{ route('user.profile.description.edit') }}" method="POST">
                @csrf
                <input type="text" name="profession" value="{{ Auth::guard('user')->user()->profession }}" placeholder="Update your profession..." autocomplete="off">
                <p class="charCount">0/100</p>
                <textarea id="description" maxlength="100" name="description" placeholder="Update your description..." autocomplete="off">{{ Auth::guard('user')->user()->description }}</textarea>
                <div class="my-2 text-center">
                    @if (Auth::guard('user')->user())
                        <button type="submit">Update</button>
                    @else
                        <a href="{{ route('login') }}"><button type="button">Update</button></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class='prfile'>
                <div class="card">
                    <div class="box">
                      <div class="cover-photo">
                        <img id="coverProview" src="{{ asset('upload/covers') }}/{{ Auth::guard('user')->user()->cover_photo == null? 'default_cover.jpg' : Auth::guard('user')->user()->cover_photo }}" alt="" />
                        <form action="{{ route('user.profile.cover.photo.edit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label for="userCoverImage">
                                <img class="upload-user-icon" src="{{ asset('assets/user.png') }}" alt="" />
                            </label>
                            <input type="file" name="user_cover_image" id="userCoverImage"
                            onchange="document.getElementById('coverProview').src = window.URL.createObjectURL(this.files[0])"
                            hidden>
                            <button type="submit">Save</button>
                        </form>
                      </div>
                    </div>
                    <div class="box">
                        <div class="content">
                            <i id="editProfileBtn" class="fa-solid fa-pen-to-square profile-edit-icon"></i>
                            <div class="text">
                                <h2>{{ Auth::guard('user')->user()->fname }} {{ Auth::guard('user')->user()->lname }}</h2>
                                <h6>{{ Auth::guard('user')->user()->profession }}</h6>
                                <h5>{{ Auth::guard('user')->user()->email }}</h5>
                                <p>{{ Auth::guard('user')->user()->description }}</p>
                            </div>
                            <ul>
                                <li>Posts <span>{{ $postCount }}</span></li>
                                <li>Followers <span>{{ $followerCount }}</span></li>
                                <li>Following <span>{{ $followingCount }}</span></li>
                            </ul>
                            @if (App\Models\Following_Follower::where('follower', Auth::guard('user')->user()->id)->where('following', Auth::guard('user')->user()->id)->first())
                                <button class="commonFollowFollowingStatus" onclick="followingFunc({{ Auth::guard('user')->user()->id }})">
                                    @lang('messages.following')
                                </button>
                            @else
                                <button class="commonFollowFollowingStatus" onclick="followingFunc({{ Auth::guard('user')->user()->id }})">
                                    @lang('messages.follow')
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="circle">
                        <div class="imgBox">
                            @if (Auth::guard('user')->user()->photo == null)
                                <img id="blah1" class="user-profile" src="{{ Avatar::create(Auth::guard('user')->user()->fname.' '.Auth::guard('user')->user()->lname)->toBase64() }}" />
                            @else
                                @if (filter_var(Auth::guard('user')->user()->photo, FILTER_VALIDATE_URL))
                                    <img id="blah2" class="user-profile" src="{{ Auth::guard('user')->user()->photo }}" alt="Google Profile Image" />
                                @else
                                    <img id="blah2" class="user-profile" src="{{ asset('upload/users') }}/{{ Auth::guard('user')->user()->photo }}" alt="Local Profile Image" />
                                @endif
                            @endif
                            <form action="{{ route('user.profile.photo.edit') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label for="userProfileImage">
                                    <img class="upload-user-icon" src="{{ asset('assets/user.png') }}" alt="" />
                                </label>
                                <input type="file" name="user_profile_image" id="userProfileImage"
                                onchange="document.getElementById('{{ Auth::guard('user')->user()->photo == null ? 'blah1' : 'blah2' }}').src = window.URL.createObjectURL(this.files[0])"
                                hidden>
                                <button type="submit">Save</button>
                            </form>
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
        const textarea = document.getElementById('description');
        const charCount = document.querySelector('.charCount');

        charCount.textContent = `${100 - textarea.value.length}/100`;

        textarea.addEventListener('input', function () {
            const maxLength = 100;
            const currentLength = textarea.value.length;

            if (currentLength >= maxLength) {
                charCount.style.color = 'red'
            } else {
                charCount.textContent = `${maxLength - currentLength}/100`;
                charCount.style.color = 'white'
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if($errors->has('profession'))
                Toastify({
                    text: "{{ $errors->first('profession') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if($errors->has('description'))
                Toastify({
                    text: "{{ $errors->first('description') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if($errors->has('user_profile_image'))
                Toastify({
                    text: "{{ $errors->first('user_profile_image') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if($errors->has('user_cover_image'))
                Toastify({
                    text: "{{ $errors->first('user_cover_image') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "pink",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('profile_photo'))
                Toastify({
                    text: "{{ session('profile_photo') }}",
                    duration: 7000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('cover_photo'))
                Toastify({
                    text: "{{ session('cover_photo') }}",
                    duration: 7000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
            @endif
            @if(session('updated'))
                Toastify({
                    text: "{{ session('updated') }}",
                    duration: 7000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                    stopOnFocus: true,
                }).showToast();
            @endif
        });
    </script>

    <script>
        const editProfileBtn = document.getElementById('editProfileBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('modal');
        editProfileBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        });
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
    </script>

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
    </script>
@endsection
