<?php

namespace Controller\Energize;

use PhpMx\View;

class Wellcome
{
    function default()
    {
        return View::render('_page/wellcome');
    }
}
