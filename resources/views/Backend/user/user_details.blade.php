@extends('Backend.layout.admin')


@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="user-profile">
                        @if ($user->photo == null)
                            <img class="user-profile" src="{{ Avatar::create($user->fname.' '.$user->lname)->toBase64() }}" />
                        @else
                            @if (filter_var($user->photo, FILTER_VALIDATE_URL))
                                <img id="blah" class="user-profile" src="{{ $user->photo }}" alt="" />
                            @else
                                <img class="user-profile" src="{{ asset('upload/users') }}/{{ $user->photo }}" alt="" />
                            @endif
                        @endif
                        <div class="user-profile-name">
                            <h4>{{ $user->fname }} {{ $user->lname }}</h4>
                            <h6>{{ $user->email }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body text-center">
                    <h6 style="color: #ff0565">{{ $user->profession }}</h6>
                    <h6>{{ $user->description }}</h6>
                    <div class="user-follower-following">
                        <button>
                            <h6>Follower</h6>
                            <h6>{{ App\Models\Following_Follower::where('follower', $user->id)->count() }}</h6>
                        </button>
                        <button>
                            <h6>Following</h6>
                            <h6>{{ App\Models\Following_Follower::where('following', $user->id)->count() }}</h6>
                        </button>
                        <button>
                            <h6>Post</h6>
                            <h6>{{ App\Models\Post::where('user_id', $user->id)->count() }}</h6>
                        </button>
                        <button>
                            <h6>Blog</h6>
                            <h6>{{ App\Models\Blog::where('user_id', $user->id)->count() }}</h6>
                        </button>
                        <button>
                            <h6>Video</h6>
                            <h6>{{ App\Models\Video::where('user_id', $user->id)->count() }}</h6>
                        </button>
                        <button>
                            <h6>Question</h6>
                            <h6>{{ App\Models\Question::where('user_id', $user->id)->count() }}</h6>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="user-details-btns">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Post</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Blog</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Question</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="pills-video-tab" data-bs-toggle="pill" data-bs-target="#pills-video" type="button" role="tab" aria-controls="pills-video" aria-selected="false">Video</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-details-main">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                @if (session('post_delete'))
                                    <div class="alert alert-success">{{ session('post_delete') }}</div>
                                @endif
                                <div class="row g-2">
                                    @forelse ($posts as $post)
                                        <div class="col-lg-4">
                                            <div class="post-item">
                                                <buttonm onclick="deleteUserPost('{{ route('delete.user.post', $post->id) }}')" class="delete-post">delete</buttonm>
                                                <a href="{{ route('show.post', $post->slug) }}">
                                                    <div class="row g-0">
                                                        @php
                                                            $imageCount = $post->rel_to_post_images->count();
                                                        @endphp
                                                        @foreach ($post->rel_to_post_images as $img)
                                                            <div class="col-lg-{{ $imageCount == 1 ? '12' : '6' }}">
                                                                <img class="post-img-img" src="{{ asset('upload/posts') }}/{{ $img->image_path }}" alt="">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="post-content">
                                                        {!! Str::limit(strip_tags($post->content), 150, '...') !!}
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <h6 class="text-danger">This is empty</h6>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade favourite-question-list-container" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                <div class="row g-2">
                                    @if (session('blog_deleted'))
                                        <div class="alert alert-success">{{ session('blog_deleted') }}</div>
                                    @endif
                                    @if (session('blog_not_found'))
                                        <div class="alert alert-warning">{{ session('blog_not_found') }}</div>
                                    @endif
                                    @forelse ($blogs as $blog)
                                        <div class="col-lg-4">
                                            <div class="blog-item">
                                                <buttonm onclick="deleteUserBlog('{{ route('delete.user.blog', $blog->id) }}')" class="delete-blog">delete</buttonm>
                                                <a href="{{ route('read.blog', $blog->slug) }}">
                                                    <img cl src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt="">
                                                    <div class="post-content">
                                                        <h4>{{ $blog->blog_title }}</h4>
                                                        {!! Str::limit(strip_tags($blog->blog_content), 150, '...') !!}
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <h6 class="text-danger">This is empty</h6>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                                <div class="row g-2">
                                    @if (session('question_success'))
                                        <div class="alert alert-success">{{ session('question_success') }}</div>
                                    @endif
                                    @if (session('question_not_found'))
                                        <div class="alert alert-warning">{{ session('question_not_found') }}</div>
                                    @endif
                                    @forelse ($questions as $question)
                                        <div class="col-lg-12">
                                            <div class="question-item">
                                                <buttonm onclick="deleteUserQuestion('{{ route('delete.user.question', $question->id) }}')" class="delete-question">delete</buttonm>
                                                <a href="{{ route('question.view', $question->slug) }}">
                                                    <div class="question-content">
                                                        <h4>{{ $question->question }}</h4>
                                                        <button class="badge badge-primary">Answers</button>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <h6 class="text-danger">This is empty</h6>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab" tabindex="0">
                                <div class="blog-favourite-container">
                                    <div class="row g-2">
                                        @if (session('video_deleted'))
                                            <div class="alert alert-success">{{ session('video_deleted') }}</div>
                                        @endif
                                        @forelse ($videos as $video)
                                            <div class="col-lg-6">
                                                <div class="video-item">
                                                    <buttonm onclick="deleteUserVideo('{{ route('delete.user.video', $video->id) }}')" class="delete-video">delete</buttonm>
                                                    <a href="{{ route('see.video', $video->slug) }}">
                                                        <div class="question-content">
                                                            <img src="{{ asset('assets/signup.jpg') }}" alt="">
                                                            {!! Str::limit(strip_tags($video->video_content), 150, '...') !!}
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @empty
                                            <h6 class="text-danger">This is empty</h6>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_script')
    <script>
        function deleteUserPost(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#579406",
                cancelButtonColor: "#ff00d4",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "The Post deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }

        function deleteUserBlog(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#579406",
                cancelButtonColor: "#ff00d4",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "The Post deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }

        function deleteUserQuestion(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#579406",
                cancelButtonColor: "#ff00d4",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "The Post deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }

        function deleteUserVideo(url) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#579406",
                cancelButtonColor: "#ff00d4",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url
                    Swal.fire({
                        title: "Deleted!",
                        text: "The Post deleted successfully.",
                        icon: "success"
                    });
                }
            });
        }
    </script>
@endsection
