<?php

// 框架 奥卡姆的剃刀
// 如无必要，毋增实体

namespace Occam;

function echo_json($code, $msg = 'ok')
{
    header('Content-Type: application/json; charset=utf-8');
    if (is_int($code)) {
        $res = compact('code', 'msg');
    } else {
        $res = ['code' => 0, 'msg' => $msg, 'data' => $code];
    }
    echo json_encode($res);
}

function render($data = [], $_file_ = null)
{
    extract($data);
    include "view/layout.html";
}

function run($router, $args)
{
    header('Content-Type: text/html; charset=utf-8');
    $router = str_replace('/', '\\', $router);
    $func = "\\Action\\$router";
    $func_method = $func.'_'.strtolower($_SERVER['REQUEST_METHOD']);
    if (function_exists($func_method)) {
        return call_user_func_array($func_method, $args);
    }
    if (!function_exists($func)) {
        $func = "\\Action\\page404";
    }
    return call_user_func_array($func, $args);
}

function get_router($module_list = [])
{
    $REQUEST_URI = $_SERVER['REQUEST_URI'];
    $path = explode('?', $REQUEST_URI)[0];
    $args = [];
    if ($path === '/') {
        return ['index', []];
    } else {
        foreach ($module_list as $module) {
            list($router, $args) = _get_router("$module/", $path);
            if ($router) {
                return [strtolower("$module/$router"), $args];
            }
        }
    }
    list($router, $args) = _get_router("", $path);
    if ($router) {
        return [strtolower($router), $args];
    }
    return ['page404', []];
}
function _get_router($module, $path)
{
    $args = [];
    if (preg_match('#^/'.$module.'([a-z][\w]*)/?$#i', $path, $matches)) {
        $router = $matches[1];
        return [$router, []];
    } elseif (preg_match('#^/'.$module.'([a-z][\w]*)/(\d+)$#i', $path, $matches)) {
        $router = $matches[1];
        $args[] = $matches[2];
        return [$router, $args];
    } else {
        return [false, false];
    }
}

spl_autoload_register(function ($c) {
    if (strpos($c, 'Occam\\') === 0) {
        $f = __DIR__."/".substr($c, 6).".php";
        require $f;
    }
});
