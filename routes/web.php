<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ExplorController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\UserAuthMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


// Languange
Route::get('/lang/{lang}', [FrontendController::class, 'setLocale']);

// Light Dark Mode
Route::get('/light/dark/mode', [FrontendController::class, 'light_dark_mode'])->name('light.dark.mode');

// user authintiaction
Route::post('/user/signup', [UserAuthController::class, 'user_signup'])->name('user.signup');
Route::post('/user/login', [UserAuthController::class, 'user_login'])->name('user.login');
Route::get('/user/logout', [UserAuthController::class, 'user_logout'])->name('user.logout');

// User
Route::post('/user/profile/photo/edit', [UserController::class, 'user_profile_photo_edit'])->name('user.profile.photo.edit');
Route::post('/user/profile/cover/photo/edit', [UserController::class, 'user_profile_cover_photo_edit'])->name('user.profile.cover.photo.edit');
Route::post('/user/profile/description/edit', [UserController::class, 'user_profile_description_edit'])->name('user.profile.description.edit');

// Fronted routes
Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/category', [FrontendController::class, 'category'])->name('category');
Route::get('/profile', [FrontendController::class, 'profile'])->name('profile')->middleware([UserAuthMiddleware::class]);
Route::get('/notification', [FrontendController::class, 'notification'])->name('notification')->middleware([UserAuthMiddleware::class]);
Route::get('/user/{slug}/{id?}', [FrontendController::class, 'user_profile'])->name('user.profile');
Route::get('/signup', [FrontendController::class, 'signup'])->name('signup')->middleware([RedirectIfAuthenticated::class]);
Route::get('/login', [FrontendController::class, 'login'])->name('login')->middleware([RedirectIfAuthenticated::class]);

// Post
Route::get('/create/post', [PostController::class, 'create_post'])->name('create.post');
Route::post('/create/post/store', [PostController::class, 'create_post_store'])->name('create.post.store');
Route::post('/update/comment/status', [PostController::class, 'update_comment_status']);
Route::post('/user/comment/store', [PostController::class, 'user_comment_store'])->name('user.comment.store');
Route::post('/user/reply/store', [PostController::class, 'user_reply_store'])->name('user.reply.store');
Route::post('/user/post/like', [PostController::class, 'user_post_like']);
Route::post('/user/following', [PostController::class, 'user_following']);
Route::get('/post/{slug}/{id?}', [PostController::class, 'show_post'])->name('show.post');
Route::post('/post/share', [PostController::class, 'post_share'])->name('post.share');
Route::get('/post/delete/delete/{id}', [PostController::class, 'post_delete'])->name('post.delete');
Route::get('/notification/delete/{id}', [PostController::class, 'notification_delete'])->name('notification.delete');
Route::post('/post/report', [PostController::class, 'post_report'])->name('post.report');

// Question
Route::get('/question-answer', [QuestionController::class, 'question_answer'])->name('qestion.answer');
Route::post('/question/store', [QuestionController::class, 'question_store'])->name('question.store');
Route::post('/answer/store', [QuestionController::class, 'answer_store'])->name('answer.store');
Route::get('/question/{slug}', [QuestionController::class, 'question_view'])->name('question.view');
Route::get('/delete/question/{id}', [QuestionController::class, 'delete_question'])->name('delete.question');

// Favourite
Route::get('/favourite', [FavouriteController::class, 'favourite'])->name('favourite');
Route::post('/add/favourite', [FavouriteController::class, 'add_favourite'])->name('add.favourite');
Route::get('/delete/favourite/question/{id}', [FavouriteController::class, 'delete_favourite_question'])->name('delete.favourite.question');
Route::get('/delete/favourite/post/{id}', [FavouriteController::class, 'delete_favourite_post'])->name('delete.favourite.post');
Route::get('/delete/favourite/blog/{id}', [FavouriteController::class, 'delete_favourite_blog'])->name('delete.favourite.blog');
Route::get('/delete/favourite/video/{id}', [FavouriteController::class, 'delete_favourite_video'])->name('delete.favourite.video');

// Blog
Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
Route::post('/create/blog', [BlogController::class, 'create_blog'])->name('create.blog');
Route::get('/see/blog', [BlogController::class, 'see_blog'])->name('see.blog');
Route::get('/delete/blog/{id}', [BlogController::class, 'delete_blog'])->name('delete.blog');
Route::get('/read/{slug}/{id?}', [BlogController::class, 'read_blog'])->name('read.blog');
Route::post('/user/blog/like', [BlogController::class, 'user_blog_like']);
Route::post('/blog/comment/store', [BlogController::class, 'blog_comment_store'])->name('blog.comment.store');
Route::post('/blog/reply/store', [BlogController::class, 'blog_reply_store'])->name('blog.reply.store');

// Friends
Route::get('/friends', [FrontendController::class, 'friends'])->name('friends')->middleware([UserAuthMiddleware::class]);

// User Category
Route::get('/education', [CategoryController::class, 'view_category'])->name('view.category');
Route::get('/see/category/{category}', [CategoryController::class, 'see_category'])->name('see.category');
Route::post('/categories/store', [CategoryController::class, 'categories_store'])->name('categories.store');

// Video
Route::get('/video', [VideoController::class, 'video'])->name('video');
Route::post('/upload/video', [VideoController::class, 'upload_video'])->name('upload.video');
Route::post('/store/video', [VideoController::class, 'store_video'])->name('store.video');
Route::get('/add/video', [VideoController::class, 'add_video'])->name('add.video');
Route::get('/compression-status', [VideoController::class, 'getCompressionStatus'])->name('getCompressionStatus');
Route::post('/user/video/like', [VideoController::class, 'user_video_like']);
Route::get('/video/{slug}/{id?}', [VideoController::class, 'see_video'])->name('see.video');
Route::post('/video/comment/store', [VideoController::class, 'video_comment_store'])->name('video.comment.store');
Route::post('/video/reply/store', [VideoController::class, 'video_reply_store'])->name('video.reply.store');
Route::get('/my-videos', [VideoController::class, 'my_videos'])->name('my.videos')->middleware([UserAuthMiddleware::class]);
Route::get('/delete/video/{id}', [VideoController::class, 'delete_video'])->name('delete.video');

// Chatting
Route::get('/message', [ChatController::class, 'message'])->name('message')->middleware([UserAuthMiddleware::class]);
Route::get('/show-chat/{recipientId}', [ChatController::class, 'show_chat'])->name('show.chat');
Route::post('/send-message/{recipientId}', [ChatController::class, 'send_message'])->name('send.message');
Route::post('/mark-as-seen/{chatId}/{recipientId}', [ChatController::class, 'mark_as_seen']);
Route::post('/set/chat/bg', [ChatController::class, 'set_chat_bg']);
Route::get('/delete-chat/{msg_id}', [ChatController::class, 'delete_chat']);

// Setting
Route::get('/setting', [SettingController::class, 'setting'])->name('setting')->middleware([UserAuthMiddleware::class]);
Route::post('/update/name', [SettingController::class, 'update_name'])->name('update.name');
Route::post('/change/password', [SettingController::class, 'change_password'])->name('change.password');
Route::post('/update/user/category', [SettingController::class, 'update_user_category'])->name('update.user.category');

// Explor
Route::get('/explor', [ExplorController::class, 'explor'])->name('explor');

// Support
Route::get('/support', [SupportController::class, 'support'])->name('support');
Route::post('/support/store', [SupportController::class, 'support_store'])->name('support.store');

// Search
Route::get('/search', [FrontendController::class, 'search'])->name('search');

// Passowrd Reset
Route::get('/password/forgot', [UserController::class, 'password_forgot'])->name('password.forgot');
Route::post('/password/forgot/req/send', [UserController::class, 'password_forgot_req_send'])->name('password.forgot.req.send');
Route::get('/password/reset/form/{token}', [UserController::class, 'password_reset_form'])->name('password.reset.form');
Route::post('/password/reset/update/{token}', [UserController::class, 'password_reset_update'])->name('password.reset.update');

// Social Login
Route::get('/google/redirect', [SocialLoginController::class, 'google_redirect'])->name('google.redirect');
Route::get('/google/callback', [SocialLoginController::class, 'google_callback'])->name('google.callback');




// Admin
Route::get('/666/admin/register', [AdminController::class, 'register'])->name('admin.register');
Route::get('/666/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/666/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// Admin authintication
Route::post('/666/admin/register', [AdminAuthController::class, 'admin_register'])->name('admin.register');
Route::post('/666/admin/login', [AdminAuthController::class, 'admin_login'])->name('admin.login');
Route::get('/666/admin/logout', [AdminAuthController::class, 'admin_logout'])->name('admin.logout');

// Language
Route::get('/666/language', [AdminController::class, 'language'])->name('language');
Route::post('/666/add/language', [AdminController::class, 'add_language'])->name('add.language');
Route::get('/666/delete/language/{id}', [AdminController::class, 'delete_language'])->name('delete.language');

// Admin Category
Route::get('/666/admin/category', [CategoryController::class, 'category'])->name('admin.category');
Route::post('/666/add/category', [CategoryController::class, 'add_category'])->name('add.category');
Route::get('/666/delete/category/{id}', [CategoryController::class, 'delete_category'])->name('delete.category');
Route::get('/666/edit/category/{id}', [CategoryController::class, 'edit_category'])->name('edit.category');
Route::post('/666/update/edit/category/{id}', [CategoryController::class, 'update_edit_category'])->name('update.edit.category');

// Chat Background
Route::get('/666/add/chat-background', [ChatController::class, 'add_chat_background'])->name('add.chat.background');
Route::post('/666/add/chat-background/store', [ChatController::class, 'add_chat_background_store'])->name('add.chat.background.store');
Route::get('/666/delete/chat-background/{id}', [ChatController::class, 'delete_chat_background'])->name('delete.chat.background');
Route::get('/666/update/status/chat-background/{id}', [ChatController::class, 'update_status_chat_background'])->name('update.status.chat.background');

// Admin User
Route::get('/666/user/list', [UserController::class, 'user_list'])->name('user.list');
Route::get('/666/admin/profile/edit', [UserController::class, 'admin_profile_edit'])->name('admin.profile.edit');
Route::post('/666/admin/profile/update', [UserController::class, 'admin_profile_update'])->name('admin.profile.update');
Route::get('/666/user/details/{user_id}', [UserController::class, 'user_details'])->name('user.details');
Route::get('/666/delete/user/post/{id}', [UserController::class, 'delete_user_post'])->name('delete.user.post');
Route::get('/666/delete/user/blog/{id}', [UserController::class, 'delete_user_blog'])->name('delete.user.blog');
Route::get('/666/delete/user/question/{id}', [UserController::class, 'delete_user_question'])->name('delete.user.question');
Route::get('/666/delete/user/video/{id}', [UserController::class, 'delete_user_video'])->name('delete.user.video');

// Logo
Route::get('/666/upload/logo', [LogoController::class, 'upload_logo'])->name('upload.logo');
Route::post('/666/add/add/logo', [LogoController::class, 'add_logo'])->name('add.logo');

// Port Reports
Route::get('/666/see/user/reports', [PostController::class, 'see_user_reports'])->name('see.user.reports');
Route::get('/666/user/reports/message/{user_id}', [PostController::class, 'user_reports_message'])->name('user.reports.message');
Route::post('/666/user/reports/message/store', [PostController::class, 'user_reports_message_store'])->name('user.reports.message.store');
Route::get('/666/user/reports/delete/{id}', [PostController::class, 'user_reports_delete'])->name('user.reports.delete');

// User Support
Route::get('/666/see/user/support', [SupportController::class, 'see_user_support'])->name('see.user.support');
Route::get('/666/user/support/delete/{id}', [SupportController::class, 'user_support_delete'])->name('user.support.delete');


// https://myaccount.google.com/apppasswords?rapt=AEjHL4MHbC09tOmWt4dUc-XVaVv_MhbtJ4UsRfut2TNdit_GPOSvr96y3FgRaKpkVr2PsbCn1wx_833PxKfUpygipzlAJNkcJ7eBMajEqkyHNVHOaqlvvl8
// hFe[6hHO03)nM8
