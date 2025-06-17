<?php

namespace Energize;

use PhpMx\Import;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

abstract class Scss
{
    protected static array $MEDIA = [
        'tablet' => 'screen and (min-width: 700px)',
        'desktop' => 'screen and (min-width: 1200px)'
    ];

    /** Compila uma string SCSS em uma string CSS */
    static function compile($style)
    {
        $style = Import::content('config.scss') . $style;

        foreach (self::$MEDIA as $media => $value)
            $style = str_replace("@media $media", "@media $value", $style);

        $scssCompiler = new Compiler();
        $scssCompiler->setOutputStyle(OutputStyle::COMPRESSED);
        return $scssCompiler->compileString($style)->getCss();
    }
}
