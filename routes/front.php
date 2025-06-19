<?php

use Energize\Router;

Router::middleware(['energize'], function () {
    Router::page('', 'energize.wellcome');
    Router::page('...', 'error.e404');

    // your front routes here

});
