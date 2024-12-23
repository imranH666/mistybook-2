@extends('Frontend.navber.navber')


@section('content')
    <div class="row category-main">
        @foreach ($categories as $category)
            <div class="col-lg-3 col-6 col-sm-4">
                <a href="{{ route('see.category', app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name) }}">
                    <div class='category-item'>
                        <img src="{{ asset('upload/categories') }}/{{ $category->category_image }}" alt="" />
                        <h5>{{ app()->getLocale() == 'en' ? $category->category_english_name : $category->category_bangla_name }}</h5>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection


@section('right_sidebar')
    @include('Frontend.layout.right_side_blogs')
    @include('Frontend.layout.recent_blog_slidebar')
@endsection

