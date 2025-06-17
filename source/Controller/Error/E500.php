<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Erro interno do servidor */
class E500
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Erro interno do servidor');
        return View::render('_base/error', ['status' => 500]);
    }
}
