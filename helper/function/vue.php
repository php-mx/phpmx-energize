<?php

if (!function_exists('vueEncapsulate')) {

    /** Prepara uma variavel PHP para ser utilizada dentro do VUEJS */
    function vueEncapsulate($value): string
    {
        return addslashes(json_encode($value));
    }
}

if (!function_exists('vueDecapsulate')) {

    /** Converte uma variavel VUEJS para ser utilizada dentro do PHP */
    function vueDecapsulate($value): ?array
    {
        return is_json($value) ? json_decode($value, true) : null;
    }
}
