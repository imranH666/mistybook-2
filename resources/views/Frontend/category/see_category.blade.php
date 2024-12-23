@extends('Frontend.navber.navber')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="carousel">
                <div class="list">
                    @foreach ($blogs as $blog)
                        <div class="item">
                            <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}">
                            <div class="content">
                                <div class="title">{{ Str::limit($blog->blog_title, 30, '...') }}</div>
                                <div class="des">
                                    {!! Str::limit(strip_tags($blog->blog_content), 200) !!}
                                </div>
                                <div class="des sm-des">
                                    {!! Str::limit(strip_tags($blog->blog_content), 100) !!}
                                </div>
                                <div class="buttons">
                                    <a href="{{ route('read.blog', $blog->slug) }}"><button>VIEW</button></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="thumbnail">
                    @foreach ($blogs as $blog)
                        <div class="item">
                            <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}">
                            <div class="content">
                                <div class="title">
                                    {{ Str::limit($blog->blog_title, 10) }}
                                </div>
                                <div class="description">
                                    {{ Str::limit($blog->rel_to_user->fname.' '.$blog->rel_to_user->lname, 10) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="arrows">
                    <button id="prev"><</button>
                    <button id="next">></button>
                </div>
                <!-- time running -->
                <div class="time"></div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row mb-3 g-3">
                @foreach ($blogs as $blog)
                    <div class="col-lg-4">
                        <a style="text-decoration: none" href="{{ route('read.blog', $blog->slug) }}">
                            <div class="see-category-blog-item">
                                <img src="{{ asset('upload/blogs') }}/{{ $blog->blog_banner }}" alt="">
                                <h4>{{ $blog->blog_title }}</h4>
                                <p>{!! Str::limit(strip_tags($blog->blog_content), 100) !!}</p>
                                <button>Read</button>
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

    <div class="recent-blog-main">
        <h5>Recent added</h5>
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
@endsection


@section('footer_script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const swiper = new Swiper('.swiper', {
                loop: true,
                slidesPerView: 2,
                spaceBetween: 10,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>

    <script>
        let nextDom = document.getElementById('next');
        let prevDom = document.getElementById('prev');

        let carouselDom = document.querySelector('.carousel');
        let SliderDom = carouselDom.querySelector('.carousel .list');
        let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
        let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
        let timeDom = document.querySelector('.carousel .time');

        thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
        let timeRunning = 3000;
        let timeAutoNext = 7000;

        nextDom.onclick = function(){
            showSlider('next');
        }

        prevDom.onclick = function(){
            showSlider('prev');
        }
        let runTimeOut;
        let runNextAuto = setTimeout(() => {
            next.click();
        }, timeAutoNext)
        function showSlider(type){
            let  SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
            let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');

            if(type === 'next'){
                SliderDom.appendChild(SliderItemsDom[0]);
                thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
                carouselDom.classList.add('next');
            }else{
                SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]);
                thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
                carouselDom.classList.add('prev');
            }
            clearTimeout(runTimeOut);
            runTimeOut = setTimeout(() => {
                carouselDom.classList.remove('next');
                carouselDom.classList.remove('prev');
            }, timeRunning);

            clearTimeout(runNextAuto);
            runNextAuto = setTimeout(() => {
                next.click();
            }, timeAutoNext)
        }
    </script>
@endsection
