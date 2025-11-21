<?php
namespace App\Services;

use OpenAI\Client;
use OpenAI;


class OpenAIService
{
    protected Client $client;
    protected string $model;

    public function __construct()
    {
        // Client::fromEnv() usa OPENAI_API_KEY do env
       $this->client = \OpenAI::client(env('OPENAI_API_KEY'));

        $this->model = env('OPENAI_MODEL', 'gpt-3.5-turbo');
    }

    /**
     * Envia prompt e retorna string da resposta.
     * $systemPrompt: instrução de sistema (opcional)
     * $userPrompt: o prompt/consulta do usuário
     */
    public function ask(string $userPrompt, array $options = []): string
    {
        $system = $options['system'] ?? 'Você é um assistente de restaurante. Responda apenas com base no cardápio fornecido. Seja sucinto e direto. Se não puder responder com os dados do cardápio, responda: "Desculpe, não tenho essa informação no cardápio."';

        $resp = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? 300,
            'temperature' => $options['temperature'] ?? 0.0,
        ]);

        return trim($resp['choices'][0]['message']['content'] ?? '');
    }
}
