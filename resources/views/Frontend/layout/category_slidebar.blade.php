

<div class="slide-main-box">
    <h5>@lang('messages.categories')</h5>
    <div class="category-slider-list">
        <div class="swiper-category">
            <div class="swiper-wrapper">
                @foreach (App\Models\Category::get() as $category)
                    <div class="swiper-slide">
                        <a href="{{ route('see.category', app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name) }}">
                            <img src="{{ asset('upload/categories') }}/{{ $category->category_image }}" alt="">
                            <p>{{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>

