<?php

namespace addons\miniform\library;

class Theme
{
    private static $config = [];

    public static function get()
    {
        if (empty(self::$config)) {
            $config = (array)json_decode(file_get_contents(self::getConfigFile()), true);
            self::$config = $config;
        }
        return self::$config;
    }

    public static function set($config, $overwrite = false)
    {
        self::$config = $overwrite ? $config : array_merge(self::$config, $config);
        file_put_contents(self::getConfigFile(), json_encode(self::$config, JSON_UNESCAPED_UNICODE));
        return self::$config;
    }

    public static function render($config)
    {
        if (isset($config['tabbar']['list']) && is_array($config['tabbar']['list'])) {
            $url = url('/', '', false, true);
            $url = preg_replace("/\/([\w]+)\.php\//i", "/", $url);
            $url = rtrim($url, "/");
            foreach ($config['tabbar']['list'] as $index => &$item) {
                $item['image'] = preg_match("/^\/assets\/addons/", $item['image']) ? $url . $item['image'] : cdnurl($item['image'], true);
                $item['selectedImage'] = preg_match("/^\/assets\/addons/", $item['selectedImage']) ? $url . $item['selectedImage'] : cdnurl($item['selectedImage'], true);
            }
        }
        return $config;
    }

    public static function getConfigFile()
    {
        return ADDON_PATH . 'miniform' . DS . 'data' . DS . 'theme.json';
    }

}
