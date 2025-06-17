<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Não implementado */
class E501
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Não implementado');
        return View::render('_base/error', ['status' => 501]);
    }
}
