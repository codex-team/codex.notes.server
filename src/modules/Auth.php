<?php

namespace App\Modules;

/**
 * @method array   generateHash()
 * @method string  generatePassword()
 * @method boolean validateCredentials()
 */
class Auth
{
    /**
     * Генерируем хеш по строке и соли
     * @param  string $input
     * @param  string $localSalt
     * @return array
     */
    public static function generateHash(string $input = '', string $localSalt = '')
    {
        global $config;

        if (!$localSalt) {
            $localSalt = self::generatePassword();
        }

        $hash = hash_hmac('sha256', $input, $config['auth']['salt'] . $localSalt, FALSE);

        return [
            'hash' => $hash,
            'localSalt' => $localSalt
        ];
    }

    /**
     * Генерируем пароль
     * @param  int|integer $length
     * @return string      hex
     */
    public static function generatePassword(int $length = 0)
    {
        global $config;

        if (!$length) {
            $length = $config['auth']['passLen'];
        }

        return substr(md5(uniqid()), 0, $length);
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