<?php

// 框架 奥卡姆的剃刀
// 如无必要，毋增实体

namespace Occam;

/**
* 
*/
class App
{
    public static $root;
    public static $action;
    public static $viewFile;

    public static function run($root)
    {
        self::$root = $root;
        include "$root/Action.php";
        list($router, $args) = self::get_router();
        self::$action = $router;
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
    private static function _get_router($module, $path)
    {
        $args = [];
        if (preg_match('#^/'.$module.'([a-z][\w]*)/?$#i', $path, $matches)) {
            $router = $matches[1];
            return [$router, []];
        } elseif (preg_match('#^/'.$module.'([a-z][\w]*)/(\w+)$#i', $path, $matches)) {
            $router = $matches[1];
            $args[] = $matches[2];
            return [$router, $args];
        } else {
            return [false, false];
        }
    }
    private static function get_router($module_list = [])
    {
        $REQUEST_URI = $_SERVER['REQUEST_URI'];
        $path = explode('?', $REQUEST_URI)[0];
        $args = [];
        if ($path === '/') {
            return ['index', []];
        } else {
            foreach ($module_list as $module) {
                list($router, $args) = self::_get_router("$module/", $path);
                if ($router) {
                    return [strtolower("$module/$router"), $args];
                }
            }
        }
        list($router, $args) = self::_get_router("", $path);
        if ($router) {
            return [strtolower($router), $args];
        }
        return ['page404', []];
    }

    public static function render($data = [], $_layout = null)
    {
        $view_root = self::$root.'/view';
        self::$viewFile = $view_root.'/'.self::$action.".html";
        extract($data);
        if (func_num_args() < 2) {
            $_layout = 'layout';
        }
        if ($_layout) {
            include "$view_root/$_layout.html";
        } else {
            include self::$viewFile;
        }
    }

    public static function json($code, $msg = 'ok')
    {
        header('Content-Type: application/json; charset=utf-8');
        if (is_int($code)) {
            $res = compact('code', 'msg');
        } else {
            $res = ['code' => 0, 'msg' => $msg, 'data' => $code];
        }
        echo json_encode($res);
    }

}
