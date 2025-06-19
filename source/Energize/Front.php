<?php

namespace Energize;

use PhpMx\Code;
use PhpMx\Request;
use PhpMx\View;

abstract class Front
{
    protected static array $HEAD = [];
    protected static array $ASIDE = [];
    protected static array $ALERT = [];

    protected static ?string $LAYOUT = null;
    protected static ?string $STATE = null;

    /** Define o layout que deve ser utilizado na criação do frontend */
    static function setLayout(string $layout): void
    {
        self::$LAYOUT = $layout;
    }

    /** Define o estado para o frontend */
    static function setState(string $state): void
    {
        self::$STATE = $state;
    }

    /** Define um valor para uma subpropriedade da tag [#HEAD] */
    static function setHead(string $name, mixed $value): void
    {
        self::$HEAD[$name] = $value;
    }

    /** Define o valor para a propriedade [#HEAD.title] */
    static function setTitle(string $title): void
    {
        self::setHead('title', $title);
    }

    /** Define o valor para a propriedade [#HEAD.favicon] */
    static function setFavicon(string $favicon): void
    {
        self::setHead('favicon', $favicon);
    }

    /** Define o valor para a propriedade [#HEAD.description] */
    static function setDescription(string $description): void
    {
        self::setHead('description', $description);
    }

    /** Define um conteúdo para uma subpropriedade da tag [#ASIDE] */
    static function setAside(string $name, string $content): void
    {
        self::$ASIDE[strtoupper($name)] = $content;
    }

    /** Adiciona um alerta para o frontend */
    static function alert(string $title, string|bool|null $content = null, ?bool $type = null): void
    {
        if (!is_string($content)) {
            $type = $content;
            $content = '';
        }

        $type = match ($type) {
            true => 'success',
            false => 'error',
            default => 'neutral'
        };

        self::$ALERT[] = [$title, $content, $type];
    }

    /** Retorna os alertas para o frontend */
    static function getAlerts(): array
    {
        return self::$ALERT;
    }

    /** Renderiza o frontend de requisição */
    static function renderize($content): string|array
    {
        if (is_array($content)) $content = implode('', $content);

        $content = self::formatHtml($content);

        if (!IS_PARTIAL)
            return self::renderizePage(self::renderizeLayout($content));

        $state = self::getStateHash();

        if (IS_FRAGMENT)
            return ['content' => $content];

        if (Request::header('Request-State') != $state)
            $content = self::renderizeLayout($content);

        return ['head' => self::$HEAD, 'state' => $state, 'content' => $content];
    }

    /** Retorna o hash do estado do frontend */
    protected static function getStateHash(): string
    {
        return Code::on([self::$LAYOUT, self::$STATE, self::$ASIDE]);
    }

    /** Renderiza o layout da resposta */
    protected static function renderizeLayout(string $content): string
    {
        $aside = [];

        foreach (self::$ASIDE as $name => $asideContent)
            $aside[$name] = self::formatHtml($asideContent);

        $layout = self::$LAYOUT;

        $layout = View::render("_base/layout/$layout", [
            'HEAD' => self::$HEAD,
            'ASIDE' => $aside
        ]);

        $layout = self::formatHtml($layout);

        return prepare($layout, [
            'CONTENT' => $content
        ]);
    }

    /** Renderiza o frontend da resposta */
    protected static function renderizePage(string $content): string
    {
        $version = cache('energize-front-version', fn() => [
            'script' => md5(View::render("_base/script")),
            'style' => md5(View::render("_base/style"))
        ]);

        $template = View::render('_base/base', [
            'HEAD' => self::$HEAD,
            'SCRIPT' => url('script.js', ['v' => $version['script']]),
            'STYLE' => url('style.css', ['v' => $version['style']]),
        ]);

        $template = self::formatHtml($template);

        return prepare($template, [
            'LAYOUT' => [
                'content' => $content,
                'state' => self::getStateHash(),
            ],
            'ALERT' => encapsulate(self::$ALERT)
        ]);
    }

    /** Formata uma estrutura HTML */
    protected static function formatHtml(string $string): string
    {
        preg_match('/<html[^>]*>(.*?)<\/html>/s', $string, $html);

        $string = count($html) ? self::formatHtmlFull($string) : self::formatHtmlFragment($string);

        $string = minifyHtml($string);

        return $string;
    }

    /** Formata uma estrutura HTML completa */
    protected static function formatHtmlFull(string $string): string
    {
        $src = [];
        $script = [];
        preg_match_all('/<script[^>]*>(.*?)<\/script>/s', $string, $tag);
        $string = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);
        foreach ($tag[1] as $key => $value)
            if (empty(trim($value)))
                $src[] = $tag[0][$key];
            else
                $script[] = $value;

        $src = implode("\n", $src ?? []);
        $script = implode("\n", $script ?? []);

        if (!empty($script)) {
            $script = minifyJs($script);
            if (!empty($script))
                $script = "<script>$script</script>\n";
        }

        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $string, $tag);
        $string = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $string);
        $style = $tag[1];

        $style = implode("\n", $style ?? []);

        if (!empty($style)) {
            $style = minifyCss($style);
            if (!empty($style))
                $style = "<style>$style</style>";
        }

        preg_match_all('/<head[^>]*>(.*?)<\/head>/s', $string, $tag);
        $string = str_replace($tag[0], '[#head]', $string);
        $string = preg_replace('#<head(.*?)>(.*?)</head>#is', '', $string);
        $head = $tag[1];

        $head[] = $style;
        $head[] = $src;
        $head[] = $script;

        $head = implode("\n", $head);
        $head = "<head>\n$head\n</head>";

        $string = prepare($string, ['head' => $head]);

        return $string;
    }

    /** Formata uma estrutura HTML em forma de fragmento */
    protected static function formatHtmlFragment(string $string): string
    {
        $src = [];
        $script = [];
        preg_match_all('/<script[^>]*>(.*?)<\/script>/s', $string, $tag);
        $string = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);
        foreach ($tag[1] as $key => $value)
            if (empty(trim($value)))
                $src[] = $tag[0][$key];
            else
                $script[] = $value;

        $src = implode("\n", $src ?? []);
        $script = implode("\n", $script ?? []);

        if (!empty($script)) {
            $script = minifyJs($script);
            if (!empty($script))
                $script = "<script>$script</script>";
        }

        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $string, $tag);
        $string = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $string);
        $style = $tag[1];

        $style = implode("\n", $style ?? []);

        if (!empty($style)) {
            $style = minifyCss($style);
            if (!empty($style))
                $style = "<style>$style</style>\n";
        }

        $string = [$src, $style, $string, $script];
        $string = implode("\n", $string);

        return $string;
    }
}
