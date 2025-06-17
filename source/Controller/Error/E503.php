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
        return View::render('_global/error/default.html', ['status' => 503]);
    }
}
