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
        <h3>Chat with User {{ $receiverId }}</h3>
        <div id="chat-box" class="border p-3 mb-3" style="height: 300px; overflow-y: scroll;"></div>
        <form id="chat-form">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $receiverId }}">
            <input type="text" name="message" id="message" class="form-control" placeholder="Type a message..." required>
            <button type="submit" class="btn btn-primary mt-2">Send</button>
        </form>
    </div>

    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:4000');

        socket.on('connect', function() {
            console.log('Connected to the chat server');
        });

        // Listen for new messages
        socket.on('newMessage', function(message) {
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML += '<div>' + message + '</div>';
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('message').value;
        const receiverId = document.querySelector('[name="receiver_id"]').value;

        // Emit the message to the Node.js server
        socket.emit('sendMessage', {
            sender_id: {{ auth()->id() }},
            receiver_id: receiverId,
            message: message,
        });

        // Clear the input field
        document.getElementById('message').value = '';
    });

    </script>
</body>
</html>
