
<div class="recent-blog-main">
    <h5>@lang('messages.recent-added')</h5>
    <div class="recent-blog-slider-list">
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach ($recent_blogs as $recent_blog)
                    <div class="swiper-slide">
                        <a href="{{ route('read.blog', $recent_blog->slug) }}">
                            <div>
                                <img src="{{ asset('upload/blogs') }}/{{ $recent_blog->blog_banner }}" alt="">
                            </div>
                            <h6>{{ Str::limit($recent_blog->blog_title, 70) }}</h6>
                            <h5>{{ $recent_blog->created_at->diffForHumans() }}</h5>
                        </a>
                    </div>
                @endforeach
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
