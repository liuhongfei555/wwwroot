<?php

namespace addons\miniform\library\alipay;

class AopEncrypt
{

    /**
     * 加密方法
     * @param string $str
     * @return string
     */


    public static function encrypt($str, $screct_key)
    {
         // openssl_encrypt 加密不同Mcrypt，对秘钥长度要求，超出16加密结果不变
        $data = openssl_encrypt($str, 'AES-128-ECB', $screct_key, OPENSSL_RAW_DATA);
        $data = strtolower(bin2hex($data));
        return $data;
    }


    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    public static function decrypt($str, $screct_key)
    {
        $decrypted = openssl_decrypt(hex2bin($str), 'AES-128-ECB', $screct_key, OPENSSL_RAW_DATA);
        return $decrypted;
    }
}
