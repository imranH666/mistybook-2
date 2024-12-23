
<div class="right-side-some-list">
    <h5>@lang('messages.do-you-like-these-blogs')</h5>
    @foreach (App\Models\Blog::inRandomOrder()->limit(5)->get() as $random_nested_blog)
        <a style="text-decoration: none" href="{{ route('read.blog', $random_nested_blog->slug) }}">
            <div class="explor-blog-2">
                <img src="{{ asset('upload/blogs') }}/{{ $random_nested_blog->blog_banner }}" alt="">
                <h6>{{ Str::limit($random_nested_blog->blog_title, 50, '...') }}</h6>
            </div>
        </a>
    @endforeach
</div>
