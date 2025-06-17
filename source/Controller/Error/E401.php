<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Requer permissão */
class E401
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Requer permissão');
        return View::render('_base/error', ['status' => 401]);
    }
}
