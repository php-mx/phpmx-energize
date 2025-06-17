<?php

namespace Energize;

abstract class Router extends \PhpMx\Router
{
    /** Adiciona uma rota para responder por chamadas do tipo página GET e POST */
    static function page(string $route, string $response, array $middlewares = []): void
    {
        if (IS_TERMINAL || (!IS_API && !IS_FRAGMENT)) {
            self::get($route, $response, $middlewares);
            self::post($route, $response, $middlewares);
        };
    }

    /** Adiciona uma rota para responder por chamadas do tipo fragmento GET e POST */
    static function fragment(string $route, string $response, array $middlewares = []): void
    {
        if (IS_TERMINAL || (!IS_API && IS_FRAGMENT)) {
            self::get($route, $response, $middlewares);
            self::post($route, $response, $middlewares);
        };
    }
}
