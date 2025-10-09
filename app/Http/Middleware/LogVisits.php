<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;

class LogVisits
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('admin/*')) {

            // Ignora assets e métodos que não sejam GET
            if ($request->ajax() || !$request->isMethod('get') || $this->isAsset($request->path())) {
                return $next($request);
            }

            // Captura o segmento da URL (primeira parte após o domínio)
            $segment = $request->segment(1) ?? '/'; // ex: "menu", "produtos", "contato"

            // Mapeamento de rotas para nomes legíveis
            $pageNames = [
                '/'        => 'Página Inicial',
                'home'     => 'Página Inicial',
                'menu'     => 'Página do Menu',
                'produtos' => 'Página de Produtos',
                'contato'  => 'Página de Contato',
            ];

            $page = $pageNames[$segment] ?? ucfirst($segment);

            // Cria uma chave única para a sessão
            $sessionKey = 'visited_' . $page;

            if (!session()->has($sessionKey)) {
                Visit::create([
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'page'       => $page,
                    'url'        => $request->fullUrl(),
                    'method'     => $request->method(),
                ]);

                session([$sessionKey => true]);
            }
        }

        return $next($request);
    }

    protected function isAsset($path)
    {
        return preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|map)$/i', $path);
    }
}
