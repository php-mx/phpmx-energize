<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Sintaxe intorreta */
class E400
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Sintaxe intorreta');
        return View::render('_base/error', ['status' => 400]);
    }
}
