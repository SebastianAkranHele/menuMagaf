<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected OpenAIService $openai;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
        $this->middleware('throttle:20,1'); // Limite de 20 requisições por minuto
    }

    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string'
        ]);

        $userMsg = trim($request->input('message'));
        $sessionId = $request->input('session_id') ?? Str::uuid();

        // 🔍 1) Busca simples no banco para evitar custo de IA
        $found = Product::where('name', 'like', "%{$userMsg}%")
            ->orWhere('description', 'like', "%{$userMsg}%")
            ->first();

        if ($found) {
            $price = is_numeric($found->price)
                ? number_format($found->price, 2, ',', '.')
                : $found->price;

            $botResponse = "😊 Sim! Temos **{$found->name}**, {$found->description} — preço: {$price} Kz.";

            if (class_exists(ChatLog::class)) {
                ChatLog::create([
                    'session_id' => $sessionId,
                    'user_message' => $userMsg,
                    'bot_response' => $botResponse,
                    'ip' => $request->ip(),
                    'meta' => json_encode(['fallback' => true, 'user_agent' => $request->userAgent()]),
                ]);
            }

            return response()->json(['session_id' => $sessionId, 'message' => $botResponse]);
        }

        // 🍽️ 2) Monta o cardápio resumido
        $products = Product::select('name', 'price', 'description')->limit(200)->get();

        $menuLines = $products->map(function ($p) {
            $price = is_numeric($p->price)
                ? number_format($p->price, 2, ',', '.')
                : $p->price;
            $desc = $p->description ? " — {$p->description}" : "";
            return "• **{$p->name}**{$desc} ({$price} Kz)";
        })->toArray();

        $menuTxt = "🍹 Aqui estão algumas das bebidas e produtos disponíveis:\n\n" . implode("\n", $menuLines);

        // 💬 3) Prompt mais natural e simpático
        $userPrompt = "Você é um assistente simpático e educado de restaurante. Responda de forma natural, clara e humana.\n"
            . "Nunca invente informações. Use apenas o cardápio abaixo.\n\n"
            . "CARDÁPIO ATUAL:\n{$menuTxt}\n\n"
            . "Pergunta do cliente: \"{$userMsg}\"\n\n"
            . "Responda de forma breve e amigável:";

        try {
            $botResponse = $this->openai->ask($userPrompt, [
                'max_tokens' => 300,
                'temperature' => 0.5 // Leve variação para parecer humano
            ]);
        } catch (\Exception $e) {
            Log::error('OpenAI error: ' . $e->getMessage());
            $botResponse = "😔 Desculpe, ocorreu um erro ao consultar o serviço de IA.";
        }

        // 🧾 4) Log opcional
        if (class_exists(ChatLog::class)) {
            ChatLog::create([
                'session_id' => $sessionId,
                'user_message' => $userMsg,
                'bot_response' => $botResponse,
                'ip' => $request->ip(),
                'meta' => json_encode(['user_agent' => $request->userAgent()]),
            ]);
        }

        return response()->json(['session_id' => $sessionId, 'message' => $botResponse]);
    }

    public function view()
    {
        return view('chatbot.index');
    }
}
