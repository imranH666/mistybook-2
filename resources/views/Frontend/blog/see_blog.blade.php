@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="see-blog-item-list-container">
                <div class="row">
                    @forelse ($blogs as $blog)
                        <div class="col-lg-4 col-sm-6">
                            <div class="see-blog-item">
                                <i onclick="deleteBlog('{{ route('delete.blog', $blog->id) }}')" class="fa-solid fa-trash delete-blog"></i>
                                <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt="">
                                <a href="{{ route('read.blog', $blog->slug) }}">
                                    <h3>{{ Str::limit($blog->blog_title, 30, '...') }}</h3>
                                    <p>{!! Str::limit(strip_tags(preg_replace('/<img[^>]*>/i', '', $blog->blog_content)), 100, ' <span href="#">See</span>') !!}</p>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="see-blog-empty-box">
                            <img src="{{ asset('assets/box.png') }}" alt="">
                            <h6>Empty</h6>
                        </div>
                    @endforelse
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
        function deleteBlog(url) {
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
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                }
            });
        }
    </script>

    <script>
        @if(session('blog_deleted'))
            Toastify({
                text: "{{ session('blog_deleted') }}",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "green",
                stopOnFocus: true,
            }).showToast();
        @endif
    </script>
@endsection
