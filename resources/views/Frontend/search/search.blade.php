@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                @forelse ($blogs as $blog)
                    <div class="col-lg-4 col-sm-6">
                        <a style="text-decoration: none" href="{{ route('read.blog', $blog->slug) }}">
                            <div class="see-category-blog-item search-item">
                                <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt="">
                                <h4>{{ $blog->blog_title }}</h4>
                                <p>{!! Str::limit(strip_tags($blog->blog_content), 100) !!}</p>
                                <button>Read</button>
                            </div>
                        </a>
                    </div>
                @empty
                    <h3 class="not-found">Not Found</h3>
                @endforelse
            </div>
        </div>
    </div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.category_slidebar')
    @include('Frontend.layout.right_side_blogs')
@endsection
