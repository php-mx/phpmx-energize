<?php

use Energize\Icon;
use PhpMx\View;

View::globalPrepare('URL', fn(...$params) => url(...$params));

View::globalPrepare('SVG', fn($iconName) => Icon::svg($iconName));
View::globalPrepare('ICON', fn($iconName, ...$styleClass) => Icon::get($iconName, ...$styleClass));

View::globalPrepare('VUE', function ($app, $name = null) {
    if (!str_starts_with($app, '.')) $app = "@vue/$app";
    return View::render("$app.vue", [], $name);
});
