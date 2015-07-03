<?php

namespace Occam;

class Service
{
    private static $lazy;
    private static $container;
    public static function set($name, $value)
    {
        if (is_callable($value)) {
            self::$lazy[$name] = $value;
        } else {
            self::$container[$name] = $value;
        }
    }
    public static function get($name)
    {
        if (isset(self::$container[$name])) {
            return self::$container[$name];
        } elseif (isset(self::$lazy[$name])) {
            $func = self::$lazy[$name];
            return self::$container[$name] = $func();
        } else {
            return null;
        }
    }

}
