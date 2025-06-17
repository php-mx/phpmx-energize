<?php

namespace Controller\Error;

use Energize\Front;
use PhpMx\View;

/** Indisponível */
class E503
{
    function default()
    {
        Front::setTitle('Ops!');
        Front::setLayout('center');
        Front::setDescription('Indisponível');
        return View::render('_base/error', ['status' => 503]);
    }
}
