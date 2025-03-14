<?php

$base_url = 'http://localhost/'; 


define('BASE_URL', $base_url);
define('CSS_PATH', BASE_URL . 'css');
define('JS_PATH', BASE_URL . 'js'); 
define('ASSETS_PATH', BASE_URL . 'assets');

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
?> 