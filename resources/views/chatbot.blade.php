<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - Menu Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #chat-box {
            height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }
        .message-user {
            text-align: right;
            margin: 10px 0;
        }
        .message-user span {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 12px 12px 0 12px;
            display: inline-block;
        }
        .message-bot {
            text-align: left;
            margin: 10px 0;
        }
        .message-bot span {
            background: #e9ecef;
            color: #212529;
            padding: 8px 12px;
            border-radius: 12px 12px 12px 0;
            display: inline-block;
            white-space: pre-line; /* mantém as quebras de linha */
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="mb-4 text-center">🤖 Chatbot - Menu Digital</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div id="chat-box">
                <div class="text-muted text-center">Comece a conversar com o assistente!</div>
            </div>

            <form id="chat-form" class="mt-3">
                <div class="input-group">
                    <input type="text" id="user-input" class="form-control" placeholder="Escreva sua pergunta..." required>
                    <button class="btn btn-primary" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const input = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');
    const userMessage = input.value.trim();

    if (!userMessage) return;

    // mostra mensagem do usuário
    chatBox.innerHTML += `<div class="message-user"><span>${userMessage}</span></div>`;
    input.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;

    // envia para o backend
    const response = await fetch('{{ url('/api/chatbot') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ message: userMessage })
    });

    const data = await response.json();
    let botMsg = data.message || "Desculpe, ocorreu um erro.";

    // converte quebras de linha e markdown simples
    botMsg = botMsg
        .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')  // negrito **texto**
        .replace(/\n/g, '<br>');                 // quebra de linha

    chatBox.innerHTML += `<div class="message-bot"><span>${botMsg}</span></div>`;
    chatBox.scrollTop = chatBox.scrollHeight;
});
</script>

</body>
</html>
