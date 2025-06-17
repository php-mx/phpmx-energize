<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Proibido */
class E403
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Acesso negado');
        return View::render('_base/error', ['status' => 403]);
    }
}
