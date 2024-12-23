
<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Mistybook</title>
    <meta name="description" content="Reset Password Email Template.">
    <style type="text/css">
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        a:hover {text-decoration: underline !important;}
        .mail-width {
            width: 95%;
        }
        .padding-bottom-class {
            height: 20px;
        }
        @media (max-width: 575.98px) {
            .mail-width {
                width: 100% !important;
            }
            .padding-bottom-class {
                height: 0 !important;
            }
        }
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px;" leftmargin="0">
    <!--100% body table-->
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f6f8fc"
        style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif; border-radius:20px;">
        <tr>
            <td>
                <table style="background-color: #222; max-width:670px; margin:0 auto; border-radius:20px;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="text-align:center">
                            <a href="{{ route('index') }}" title="Mistybook" target="_blank">
                                <img width="100" src="https://i.postimg.cc/9f53RFtn/logo1.png" title="Mistybook" alt="Mistybook">
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table class="mail-width" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px;background:#000000; padding:10px; border-radius:3px; text-align:left;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06); margin:0 auto;">
                                <tr>
                                    <td style="height:20px;">&nbsp;</td>
                                </tr>
                                <table border="0" align="center" cellpadding="0" cellspacing="0">
                                    @php
                                        $getUser = App\Models\User::where('id', $content['user_id'])->first();
                                    @endphp
                                    <tr style="margin-bottom: 10px;display:inline-block;">
                                        <td><a style="color: #fff; text-decoration:none" href="{{ route('user.profile', $getUser->slug) }}"><img style="width: 40px; height:40px; border-radius:50%; margin-right: 10px;" src="https://i.postimg.cc/L6QJyHkS/blog-banner-676563c564399.jpg" alt=""></a></td>
                                        <td>
                                            <a style="color: #fff; text-decoration:none" href="{{ route('user.profile', $getUser->slug) }}"><h4 style="color: #fff; font-weight:600; font-size:18px">{{ $getUser->fname }} {{ $getUser->lname }}</h4></a>
                                            <span style="color: gray">{{ $content->created_at->format('D, M d, Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                <tr>
                                    <td>
                                        <a style="color: #fff; text-decoration:none" href="{{ route('read.blog', $content['slug']) }}">
                                            <h3 style="color:#fff; font-weight:500; margin:0;font-size:25px;font-family:'Rubik',sans-serif;">{{ $content['blog_title'] }}</h3>
                                        </a>
                                        <span
                                            style="display:inline-block; vertical-align:middle;border-bottom:1px solid #cecece; width:100%;">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="{{ route('read.blog', $content['slug']) }}">
                                            <img width="100%" height="150px" src="https://i.postimg.cc/L6QJyHkS/blog-banner-676563c564399.jpg" alt="Image">
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#ecf2f5;line-height:24px;font-size:15px;display:inline-block;margin-top:10px">
                                        {!! $content['blog_content'] !!}
                                        <a style="color: #fff; font-size: 16px; font-weight:700; text-decoration:none; background:gray; padding:2px 20px; border-radius:5px" href="{{ route('read.blog', $content['slug']) }}">@lang('messages.see-more')</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><h3 style="color: #fff; margin:20px 0;font-size:20px">Maybe you are interested in these blogs</h3></td>
                                </tr>
                                <table border="0" align="center" cellpadding="0" cellspacing="0">
                                    @foreach ( App\Models\Blog::where('category_id', $content['category_id'])->where('slug', '!=', $content['slug'])->inRandomOrder()->limit(6)->get() as $blog)
                                        <tr style="margin:5px 0;display: block;">
                                            <td>
                                                <a href="{{ route('read.blog', $blog->slug) }}">
                                                    <img width="60px;" height="40px" style="margin-right:10px;border-radius:5px" src="https://i.postimg.cc/L6QJyHkS/blog-banner-676563c564399.jpg" alt="Image">
                                                </a>
                                            </td>
                                            <td style="font-size: 15px; margin-left:10px; color: #fff">
                                                <a style="text-decoration: none" href="{{ route('read.blog', $blog->slug) }}">
                                                    {{ $blog->blog_title }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>

                                <tr>
                                    <td><h3 style="color: #fff; margin:20px 0;font-size:20px">Something Special for You</h3></td>
                                </tr>
                                <table border="0" align="center" cellpadding="0" cellspacing="0">
                                    @foreach ( App\Models\Blog::inRandomOrder()->latest()->limit(5)->get() as $blog)
                                        <tr style="margin:5px 0;display: block;">
                                            <td>
                                                <a href="{{ route('read.blog', $blog->slug) }}">
                                                    <img width="60px;" height="40px" style="margin-right:10px;border-radius:5px" src="https://i.postimg.cc/L6QJyHkS/blog-banner-676563c564399.jpg" alt="Image">
                                                </a>
                                            </td>
                                            <td style="font-size: 15px; margin-left:10px; color: #fff">
                                                <a style="text-decoration: none" href="{{ route('read.blog', $blog->slug) }}">
                                                    {{ $blog->blog_title }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                 <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                        <td class="padding-bottom-class">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!--/100% body table-->
</body>

</html>
