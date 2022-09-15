<?php

namespace addons\miniform\library;

use Hashids\Hashids;

class IntCode
{
    private static $hasids = null;

    /**
     * 初始化
     * @access public
     * @return Hashids
     */
    public static function hashids()
    {
        if (is_null(self::$hasids)) {
            $config = get_addon_config('miniform');
            $key = $config['verification_key'];
            $key = $key ? $key : config('token.key');
            self::$hasids = new Hashids($key, 32);
        }

        return self::$hasids;
    }

    public static function encode($int)
    {
        return self::hashids()->encode($int);
    }

    public static function decode($str)
    {
        $data = self::hashids()->decode($str);
        if (isset($data[0])) {
            return $data[0];
        }
        return null;
    }
}
