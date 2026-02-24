<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/mercadopago',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Renderizar erros mais descritivos para o painel admin MoonShine
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                $message = $e->getMessage();
                // Extrair mensagem amigável de erros SQL comuns
                if (str_contains($message, 'Column not found')) {
                    preg_match("/Unknown column '(.+?)'/", $message, $matches);
                    $col = $matches[1] ?? 'desconhecida';
                    $friendly = "Coluna '{$col}' não existe na tabela. Verifique se as migrations foram executadas.";
                } elseif (str_contains($message, 'Duplicate entry')) {
                    preg_match("/Duplicate entry '(.+?)' for key '(.+?)'/", $message, $matches);
                    $friendly = "Registro duplicado: '{$matches[1]}' já existe (campo: {$matches[2]}).";
                } elseif (str_contains($message, 'cannot be null')) {
                    preg_match("/Column '(.+?)' cannot be null/", $message, $matches);
                    $friendly = "Campo obrigatório não preenchido: '{$matches[1]}'.";
                } elseif (str_contains($message, 'Data too long')) {
                    preg_match("/Data too long for column '(.+?)'/", $message, $matches);
                    $friendly = "Texto muito longo para o campo '{$matches[1]}'.";
                } elseif (str_contains($message, 'a]foreign key constraint fails')) {
                    $friendly = "Não é possível salvar: referência a registro inexistente.";
                } else {
                    $friendly = "Erro no banco de dados: " . \Illuminate\Support\Str::limit($message, 200);
                }
                \MoonShine\Laravel\MoonShineRequest::session()?->flash('alert', json_encode(['type' => 'error', 'message' => $friendly]));
                return back()->withInput()->with('toast', ['type' => 'error', 'message' => $friendly]);
            }
        });
    })->create();
