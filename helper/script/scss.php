<?php

use PhpMx\View;
use PhpMx\ViewRender\ViewRenderCss;

View::$RENDER_EX_CLASS['scss'] = ViewRenderCss::class;
