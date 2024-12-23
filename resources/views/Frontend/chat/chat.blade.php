@extends('Frontend.navber.navber')


@section('content')
    <div class="row g-2">
        <div class="col-lg-5 col-sm-4 col-md-4 sm-position">
            <div class="chatting-friend-container">
                <ul>
                    @foreach ($users as $user)
                        <li class="last-user-message-{{ $user->id }}">
                            <a href="{{ route('user.profile', $user->slug) }}">
                                @if ($user->photo == null)
                                    <img class="user-profile" src="{{  Avatar::create($user->fname.' '.$user->lname)->toBase64() }}" />
                                @else
                                    @if (filter_var($user->photo, FILTER_VALIDATE_URL))
                                        <img src="{{ $user->photo }}" alt="">
                                    @else
                                        <img src="{{ asset('upload/users') }}/{{ $user->photo }}" alt="">
                                    @endif
                                @endif
                            </a>
                            <div onclick="sendUserId({{ $user->id }})" class="chat-friend-name chat-friend-name-lg">
                                <h5>{{ $user->fname.' '.$user->lname }}</h5>
                                @if ($user->last_message)
                                    <h6>
                                        {{ $user->last_message->message }}
                                        <span class="message-time">
                                            {{ $user->last_message ? $user->last_message->created_at->format('M d, Y') : '' }}
                                        </span>
                                    </h6>
                                @else
                                    <h6>No messages yet</h6>
                                @endif
                            </div>

                            <div onclick="sendUserId({{ $user->id }})" class="chat-friend-name chat-friend-name-sm">
                                <h5>{{ $user->fname.' '.$user->lname }}</h5>
                                @if ($user->last_message)
                                    <h6>
                                        {{ $user->last_message->message }}
                                        <span class="message-time">
                                            {{ $user->last_message ? $user->last_message->created_at->format('M d, Y') : '' }}
                                        </span>
                                    </h6>
                                @else
                                    <h6>No messages yet</h6>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-lg-7 col-sm-8 col-md-8 sm-top-padding">
            @php
                $isChatBG = App\Models\Set_Chat_Bg::where('user_id', Auth::guard('user')->user()->id)->first();
            @endphp
            <div class="chat-container" style="background: url({{ $isChatBG? asset('upload/chat-backgrounds/'.$isChatBG->rel_to_chat_bg->chat_bg) : 'var(--icon-bg-color)' }}) no-repeat center / cover">
                <div class="chat-heading">
                    <img class="chat-heading-img" src="{{ asset('assets/signup.jpg') }}" alt="">
                    <div class="chat-heading-name">
                        <i class="fa-solid fa-circle-info change-chat-bg-btn"></i>
                        <i class="fa-solid fa-arrow-left back-btn"></i>
                        <h5>Chats</h5>
                        <h6>You are available to chat here.</h6>
                    </div>
                    <div class="cahtting-bg-container">
                        <div class="row g-2">
                            @foreach ($chat_bgs as $chat_bg)
                                @if ($chat_bg->status == 0)
                                    <div class="col-lg-4 col-6 col-sm-4 col-md-4">
                                        <img onclick="setChatBg({{ $chat_bg->id }})" src="{{ asset('upload/chat-backgrounds') }}/{{ $chat_bg->chat_bg }}" alt="">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="show-chat">
                    <div class="chat-user-profile">
                        <img>
                        <h4></h4>
                        <h6>You are now connected on chatting</h6>
                    </div>

                    <ul class="chat-list" id="chat-list"></ul>

                </div>
                <div class="previewChatImage">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <img src="" alt="">
                </div>
                <div class="chat-input-box">
                    <label for="imageIcon">
                        <img src="{{ asset('assets/photo.png') }}" alt="">
                    </label>
                    <input type="file" name="chat_img" id="imageIcon" onchange="previewChatImage(event)" hidden>
                    <input type="text" name="" id="message-input">
                    <button id="send-btn"><img src="{{ asset('assets/send.png') }}" alt=""></button>
                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const users = @json($users);
            const params = new URLSearchParams(window.location.search);
            const userSlug = params.get('user');

            users.forEach(user => {
                if(user.slug === userSlug) {
                    sendUserId(user.id);
                    if ($(window).width() < 576) {
                        $('.chatting-friend-container').fadeOut(500, 'swing');
                    }
                }
            });
        });
    </script>

    <script>
        $('.change-chat-bg-btn').click(function () {
            $('.cahtting-bg-container').fadeToggle(500, 'swing');
        })
        $('.back-btn').click(function () {
            $('.chatting-friend-container').fadeToggle(500, 'swing');
        })
        $('.chat-friend-name-sm').click(function () {
            $('.chatting-friend-container').fadeOut(500, 'swing');
        })

        function setChatBg(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/set/chat/bg',
                type: 'POST',
                data: {
                    chat_bg_id: id,
                },
                success: function(response) {
                    if(response) {
                        $('.chat-container').css('background', `url(${response}) no-repeat center / cover`)
                    }else {
                        console.log('something is error');
                    }
                },
            });
        }
    </script>

    <script>
        let recipientId = null;
        const currentUserId = {{ Auth::guard('user')->user()->id }};

        let image_name = '';
        function previewChatImage(event) {
            document.querySelector('.previewChatImage').style.display = 'block';
            document.querySelector('.previewChatImage img').src = window.URL.createObjectURL(event.target.files[0]);
            image_name = event.target.files[0];
        }

        $('.previewChatImage i').click(function() {
            $('.previewChatImage').css('display', 'none')
            image_name = '';
        })

        function sendUserId(user_id) {
            recipientId = user_id;
            let messages = [];
            $('.chat-list').hide();

            if (!document.getElementById(`chat-list-${user_id}`)) {
                $('.show-chat').append(`<ul class="chat-list" id="chat-list-${user_id}"></ul>`);
            }

            $(`#chat-list-${user_id}`).show();

            $.ajax({
                url: `/show-chat/${user_id}`,
                type: "GET",
                success: function (response) {
                    messages = response.messages;
                    const chatList = $(`#chat-list-${user_id}`);
                    chatList.html('');

                    const imageLoadPromises = []; // ইমেজ লোড ট্র্যাক করার জন্য

                    messages.forEach(message => {
                        let newMessage = '';
                        const messageDate = new Date(message.created_at);
                        const formattedTime = messageDate.toLocaleString('en-US', {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });

                        if (message.sender_id === currentUserId) {
                            const chat_image = message.image
                                ? `<a href="upload/chats/${message.image}" data-lightbox="roadtrip-${user_id}"> <img class="chat-image-size" src="upload/chats/${message.image}" alt="User Image"> </a>`
                                : '';

                            newMessage = `<li data-chat-id="${message.id}" class="chat-right ${message.see ? 'seen' : ''}">
                                ${message.message? message.message : ''}
                                <i class="fa-solid fa-check"></i>
                                <i onclick="deleteChat(${message.id})" class="fa-solid fa-trash-can chat-three-dot-menu"></i>
                                <span class="chat-time">${formattedTime}</span>
                                ${chat_image}
                            </li>`;
                        } else {
                            const chat_image = message.image
                                ? `<a href="upload/chats/${message.image}" data-lightbox="roadtrip-${user_id}"> <img class="chat-image-size" src="upload/chats/${message.image}" alt="User Image"> </a>`
                                : '';
                            const imagePath = message.recipient_image
                                ? (message.recipient_image.startsWith('http')
                                    ? message.recipient_image
                                    : `/upload/users/${message.recipient_image}`)
                                : response.avatar;


                            newMessage = `<li data-chat-id="${message.id}" class="chat-left">
                                <img class="chat-profile-img" src="${imagePath}" alt="User Profile">
                                ${message.message? message.message : ''}
                                <span class="chat-time">${formattedTime}</span>
                                <i onclick="deleteChat(${message.id})" class="fa-solid fa-trash-can chat-three-dot-menu"></i>
                                ${chat_image}
                            </li>`;
                        }

                        chatList.append(newMessage);

                        // ইমেজ লোড প্রমিজ যোগ করা
                        const imgElement = chatList.find(`img[src="upload/chats/${message.image}"]`);
                        if (imgElement.length) {
                            const imgPromise = new Promise(resolve => {
                                imgElement.on('load', resolve);
                            });
                            imageLoadPromises.push(imgPromise);
                        }
                    });

                    const recipient = response.recipient;
                    const recipientImage = recipient.photo
                        ? (recipient.photo.startsWith('http')
                            ? recipient.photo
                            : `{{ asset('upload/users') }}/${recipient.photo}`)
                        : response.avatar;

                    $('.chat-heading .chat-heading-img').attr('src', recipientImage);
                    $('.chat-user-profile img').attr('src', recipientImage);

                    $('.chat-heading-name h5').html(`${recipient.fname} ${recipient.lname || ''}`);
                    $('.chat-user-profile h4').html(`${recipient.fname} ${recipient.lname || ''}`);

                    initializeAbly(user_id);

                    // সব ইমেজ লোড হওয়ার পরে স্ক্রোল
                    Promise.all(imageLoadPromises).then(scrollToBottom).catch(scrollToBottom);
                },
                error: function () {
                    alert('Unable to load chat. Please try again later.');
                }
            });

        }

        let activeChannel = null;
        function initializeAbly(user_id) {
            const currentUserId = {{ Auth::guard('user')->user()->id }};
            const channelName = `private-${Math.min(currentUserId, user_id)}-${Math.max(currentUserId, user_id)}`;

            if (activeChannel && activeChannel.name === channelName) {
                return;
            }
            const ably = new Ably.Realtime('{{ $ablyApiKey }}');

            if (activeChannel) {
                activeChannel.unsubscribe();
            }

            const channel = ably.channels.get(channelName);
            activeChannel = channel;

            channel.subscribe('message', function (message) {
                const chat_image = message.data.chat_image
                    ? `<a href="${message.data.chat_image}" data-lightbox="roadtrip"> <img class="chat-image-size" src="${message.data.chat_image}" alt="User Image"> </a>`
                    : '';
                const messageList = document.getElementById(`chat-list-${user_id}`);
                const newMessage = document.createElement('li');

                const senderId = message.data.sender_id;
                if (senderId === currentUserId) {
                    newMessage.className = "chat-right";
                    newMessage.setAttribute('data-chat-id', message.data.chat_id);
                    newMessage.innerHTML = `
                        ${message.data.message}
                        <i class="fa-solid fa-check"></i>
                        ${chat_image}
                        `;
                } else {
                    markAsSeen(message.data.chat_id);
                    newMessage.className = "chat-left";
                    if (message.data.sender_image.startsWith('data:image')) {
                        newMessage.innerHTML = `<img class="chat-profile-img" src="${message.data.sender_image}"> ${message.data.message} ${chat_image}`;
                    } else {
                        const senderImage = message.data.sender_image.startsWith('http')
                            ? message.data.sender_image
                            : `{{ asset('upload/users') }}/${message.data.sender_image}`;

                        newMessage.innerHTML = `<img class="chat-profile-img" src="${senderImage}"> ${message.data.message} ${chat_image}`;
                    }

                }
                messageList.appendChild(newMessage);
                const img = newMessage.querySelector('img.chat-image-size');
                if (img) {
                    img.onload = () => scrollToBottom();
                } else {
                    scrollToBottom();
                }
            });

            channel.subscribe('message_update', function (message) {
                const seenBadge = document.querySelector(`li[data-chat-id="${message.data.chat_id}"]`);
                seenBadge.className = 'chat-right seen'
            })
        }

        document.getElementById('send-btn').addEventListener('click', function () {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();

            if (!message && !image_name) {
                Toastify({
                    text: "Message cannot be empty!",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "orange",
                    stopOnFocus: true,
                }).showToast();
                return;
            }
            if (recipientId === null) {
                Toastify({
                    text: "Please, select a user",
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "purple",
                    stopOnFocus: true,
                }).showToast();
                return;
            }

            const formData = new FormData();
            if(message) {
                formData.append('message', message);
            }
            if(image_name) {
                formData.append('image_name', image_name);
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF টোকেন
                }
            });
            $.ajax({
                url: `/send-message/${recipientId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status === 'success') {
                        // console.log('Message sent successfully:', data);
                    } else {
                        console.error('Error from server:', data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });

            messageInput.value = '';
            document.querySelector('.previewChatImage').style.display = 'none';
            image_name = '';
            scrollToBottom();
        });


        function markAsSeen(chatId) {
            fetch(`/mark-as-seen/${chatId}/${recipientId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // console.log('devil-2', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function deleteChat(msg_id) {
            $.ajax({
                url: `/delete-chat/${msg_id}`,
                type: "GET",
                success: function (response) {
                    if(response.status === 'success') {
                        $(`li[data-chat-id="${msg_id}"]`).css('display', 'none')
                    }else {
                        console.log(response.message);
                    }
                },
                error: function () {
                    alert('Unable to delete chat. Please try again later.');
                }
            });
        }

        function scrollToBottom() {
            const messageList = document.querySelector('.show-chat');
            messageList.scrollTop = messageList.scrollHeight;
        }
    </script>

@endsection
