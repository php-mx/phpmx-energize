<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Método não permitido */
class E405
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Metodo não permitido');
        return View::render('_global/error/default.html', ['status' => 405]);
    }
}
