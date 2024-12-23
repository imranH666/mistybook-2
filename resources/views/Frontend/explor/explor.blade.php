@extends('Frontend.navber.navber')


@section('content')
    <div class="row g-2">
        <div class="col-lg-4 col-sm-4">
            <div class="row">
                @foreach ($random_blogs as $index => $random_blog)
                    @if ($index%2 == 0)
                        <div class="col-lg-12 col-8 col-sm-12">
                            <a style="text-decoration: none" href="{{ route('read.blog', $random_blog->slug) }}">
                                <div class="explor-blog">
                                    <img data-src="{{ asset('upload/blogs') }}/{{ $random_blog->blog_banner }}" src="" class="explor_img_lazy" alt="">
                                    <h5 class="lazy_content_load" data-content="{{ Str::limit($random_blog->blog_title, 100, '...') }}">Loading...</h5>
                                </div>
                            </a>
                        </div>
                    @else
                        @foreach (App\Models\Blog::inRandomOrder()->limit(rand(1, 5))->get() as $random_nested_blog)
                            <div class="col-lg-12">
                                <a style="text-decoration: none" href="{{ route('read.blog', $random_nested_blog->slug) }}">
                                    <div class="explor-blog-2">
                                        <img data-src="{{ asset('upload/blogs') }}/{{ $random_nested_blog->blog_banner }}" src="" alt="" class="explor_img_lazy">
                                        <h6 class="lazy_content_load" data-content="{{ Str::limit($random_nested_blog->blog_title, 50, '...') }}"></h6>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="row">
                @foreach ($random_blogs as $index => $random_blog)
                    @if ($index%2 == 0)
                        @foreach (App\Models\Blog::inRandomOrder()->limit(rand(1, 5))->get() as $random_nested_blog)
                            <div class="col-lg-12">
                                <a style="text-decoration: none" href="{{ route('read.blog', $random_nested_blog->slug) }}">
                                    <div class="explor-blog-2">
                                        <img data-src="{{ asset('upload/blogs') }}/{{ $random_nested_blog->blog_banner }}" src="" class="explor_img_lazy" alt="">
                                        <h6 class="lazy_content_load" data-content="{{ Str::limit($random_nested_blog->blog_title, 50, '...') }}"></h6>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="col-lg-12 col-8 col-sm-12">
                            <a style="text-decoration: none" href="{{ route('read.blog', $random_blog->slug) }}">
                                <div class="explor-blog second">
                                    <img data-src="{{ asset('upload/blogs') }}/{{ $random_blog->blog_banner }}" src="" class="explor_img_lazy" alt="">
                                    <h5 class="lazy_content_load" data-content="{{ Str::limit($random_blog->blog_title, 100, '...') }}">Loading...</h5>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="row">
                @foreach (App\Models\Blog::inRandomOrder()->get() as $random_nested_blog)
                    <div class="col-lg-12">
                        <a style="text-decoration: none" href="{{ route('read.blog', $random_nested_blog->slug) }}">
                            <div class="explor-blog-2 third">
                                <img data-src="{{ asset('upload/blogs') }}/{{ $random_nested_blog->blog_banner }}" src="" class="explor_img_lazy" alt="">
                                <h6 class="lazy_content_load" data-content="{{ $random_nested_blog->blog_title }}"></h6>
                            </div>
                        </a>
                    </div>
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
        function lazy_Loading_img(class_name) {
            new LazyLoad({
                elements_selector: `.${class_name}`
            });
        }
        lazy_Loading_img('explor_img_lazy');

        function lazy_Loading_Content(class_name) {
            new LazyLoad({
                elements_selector: `.${class_name}`,
                callback_enter: function (element) {
                    const content = element.getAttribute("data-content");
                    if (content) {
                        element.textContent = content;
                        element.classList.remove(class_name);
                    }
                }
            });
        }
        lazy_Loading_Content('lazy_content_load');

    </script>
@endsection
