<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Não encontrado */
class E404
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Não encontrado');
        return View::render('_global/base/error', ['status' => 404]);
    }
}
