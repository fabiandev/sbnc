<?php
namespace sbnc\helpers;

/**
 * Class Helpers
 *
 * Functions used by sbnc
 *
 * @package sbnc\helpers
 */
class Helpers
{

    public static function isPost()
    {
        return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0 ? false : true;
    }

    public static function isGet()
    {
        return !self::isPost();
    }

    public static function randomKey($from, $to)
    {
        return chr(rand(97, 122)) . substr(md5(microtime()), rand(0, 26), rand($from, $to));
    }

    public static function requestMethod($to_lower = false)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        return $to_lower ? strtolower($method) : $method;
    }

    public static function isEmpty($value)
    {
        return (strlen(trim($value)) == 0);
    }

    public static function filter($value, $nl2br = false)
    {
        if ($nl2br) {
            return !self::isEmpty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !self::isEmpty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

    public static function getUrl()
    {
        $url = 'http';
        $url .= (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://');
        $url .= $_SERVER['HTTP_HOST'];
        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

}
