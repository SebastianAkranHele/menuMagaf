@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-header bg-dark text-white text-center">
            🤖 Chatbot - Menu Digital
        </div>
        <div class="card-body" id="chat-window" style="height: 400px; overflow-y: auto;">
            <div id="messages"></div>
        </div>
        <div class="card-footer">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control" placeholder="Digite sua mensagem...">
                <button class="btn btn-dark" id="send-btn">Enviar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('send-btn').addEventListener('click', sendMessage);
document.getElementById('user-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});

async function sendMessage() {
    const input = document.getElementById('user-input');
    const msg = input.value.trim();
    if (!msg) return;

    addMessage('Você', msg, 'text-end text-primary');
    input.value = '';

    const response = await fetch('{{ url('/api/chatbot') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message: msg }),
    });

    const data = await response.json();
    addMessage('Bot', data.reply, 'text-start text-dark');
}

function addMessage(sender, text, alignClass) {
    const msgBox = document.getElementById('messages');
    const div = document.createElement('div');
    div.className = 'my-2 ' + alignClass;
    div.innerHTML = `<strong>${sender}:</strong> ${text}`;
    msgBox.appendChild(div);
    msgBox.scrollTop = msgBox.scrollHeight;
}
</script>
@endsection
