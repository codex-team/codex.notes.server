<?php

namespace App\Versions\V1\Utilities;

use App\System\Utilities\Config;

/**
 * @method array   generateHash()
 * @method string  generatePassword()
 * @method boolean validateCredentials()
 */
class Tweaks
{
    /**
     * Генерируем хеш по строке и соли
     * @param  string $input
     * @param  string $localSalt
     * @return array
     */
    public static function generateHash(string $input = '', string $localSalt = '')
    {
        $config = Config::load('user');

        if (!$localSalt) {
            $localSalt = self::generatePassword();
        }

        $hash = hash_hmac('sha256', $input, $config['auth']['salt'] . $localSalt, false);

        return [
            'hash' => $hash,
            'localSalt' => $localSalt
        ];
    }

    /**
     * Генерируем пароль
     * @param  integer     $length
     * @return string      hex
     */
    public static function generatePassword(int $length = 0)
    {
        $config = Config::load('user');

        if (!$length) {
            $length = $config['auth']['password']['length'];
        }

        $string = self::generateHex($length);

        return substr($string, 0, $length);
    }

    /**
     * Generate hex
     * @param  int|integer $length
     * @return string hex
     */
    public static function generateHex(int $length = 8)
    {
        $bytes = floor($length / 2);

        return bin2hex(openssl_random_pseudo_bytes($bytes));
    }

    /**
     * Проверяем на присутствие пароля и идентификатора
     * ¯\_(ツ)_/¯
     * @param  string $userId
     * @param  string $password
     * @return boolean
     */
    public static function validateCredentials(string $userId = '', string $password = '')
    {
        if (!$userId || !$password) {
            return FALSE;
        }

        return TRUE;
    }
}