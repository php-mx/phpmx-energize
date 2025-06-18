<?php

namespace Controller\Energize;

use Energize\Front;
use PhpMx\View;

class Wellcome
{
    function default()
    {
        Front::alert('teste', 'teste', true);
        return View::render('_page/wellcome');
    }
}
