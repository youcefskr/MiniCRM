<!DOCTYPE html>
<html>
<head>
    <title>AI Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>CRM Chatbot Test</h2>
    <div id="chat"></div>

    <form id="chat-form">
        <input type="text" id="message" placeholder="Type your message..." required />
        <button type="submit">Send</button>
    </form>

    <script>
        const form = document.getElementById('chat-form');
        const chat = document.getElementById('chat');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('message').value;
            chat.innerHTML += `<p><b>You:</b> ${msg}</p>`;

            const res = await fetch('/api/chatbot/respond', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await res.json();
            console.log(data);
            chat.innerHTML += `<p><b>AI:</b> ${data.response}</p>`;
            document.getElementById('message').value = '';
        });
    </script>
</body>
</html>
