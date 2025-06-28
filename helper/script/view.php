<?php

use Energize\Icon;
use PhpMx\Code;
use PhpMx\View;
use PhpMx\ViewRender\ViewRenderVue;

View::$RENDER_EX_CLASS['vue'] = ViewRenderVue::class;

View::globalPrepare('URL', fn(...$params) => url(...$params));

View::globalPrepare('SVG', fn($iconName) => Icon::svg($iconName));
View::globalPrepare('ICON', fn($iconName, ...$styleClass) => Icon::get($iconName, ...$styleClass));

View::globalPrepare('FORM', fn($name) => prepare("data-form-key='[#]' method='post'", Code::on(["form-$name", url('.')])));

View::globalPrepare('VUE', fn($app, $name) => View::render("$app.vue", [], ['name' => $name]));
