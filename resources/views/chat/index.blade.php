<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <h5>Users</h5>
                <ul class="list-group">
                    @foreach($users as $u)
                        <li class="list-group-item">
                            <a href="{{ url('/chat/' . $u->id) }}">{{ $u->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-9">
                <h5>Chat with {{ $receiver ? $receiver->name : 'Select a user' }}</h5>
                <div id="chat-box" class="border p-3 mb-3" style="height: 400px; overflow-y: scroll;">
                    @foreach($messages as $message)
                        <div>
                            <strong>{{ $message->sender_id == $user->id ? 'You' : $receiver->name }}:</strong> {{ $message->body }}
                        </div>
                    @endforeach
                </div>
                @if($receiver)
                    <form id="chat-form">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">
                        <input type="text" id="message" name="message" class="form-control" placeholder="Type your message..." autocomplete="off" required>
                        <button type="submit" class="btn btn-primary mt-2">Send</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <script src="/js/echo.js"></script>
    <script>
        const userId = {{ Auth::id() }};
        const receiverId = {{ $receiver ? $receiver->id : 'null' }};
        const chatBox = document.getElementById('chat-box');

        // Initialize Laravel Echo and Pusher
        const echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("PUSHER_APP_KEY") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });

        if (receiverId) {
            echo.private(`chat.${receiverId}`)
                .listen('MessageSent', (e) => {
                    if (e.message.sender_id == receiverId || e.message.receiver_id == receiverId) {
                        const messageElement = `<div><strong>${e.message.sender_id == userId ? 'You' : '{{ $receiver->name }}'}:</strong> ${e.message.body}</div>`;
                        chatBox.innerHTML += messageElement;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });
        }

        // Handle form submission
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/send-message', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const messageElement = `<div><strong>You:</strong> ${data.message.body}</div>`;
                chatBox.innerHTML += messageElement;
                chatBox.scrollTop = chatBox.scrollHeight;
                document.getElementById('message').value = '';
            });
        });
    </script>
</body>
</html>
