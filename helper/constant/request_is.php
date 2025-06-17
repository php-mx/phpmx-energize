<?php

use PhpMx\Request;

/** Se a requisição é uma solicitação página parcial */
define('IS_PARTIAL', !IS_TERMINAL && Request::header('Request-Partial'));

/** Se a requisição é uma solicitação de fragmento */
define('IS_FRAGMENT', !IS_TERMINAL && Request::header('Request-Fragment'));

/** Se a requisição é uma solicitação de api */
define('IS_API', !IS_TERMINAL && Request::header('Request-Api'));
