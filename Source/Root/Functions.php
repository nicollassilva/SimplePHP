<?php

namespace SimplePHP\Root;

/**
 * Trait Functions
 * @package NicollasSilva/SimplePHP
 */
trait Functions {
    /**
     * @param string $hash
     * @param array $options
     * @return null|string
     */
    public function hashFormat(String $hash, Array $options = ['cost' => 15]): ?String
    {
        return password_hash($hash, PASSWORD_BCRYPT, $options);
    }

    /**
     * @param string $text
     * @return null|string
     */
    public function urlFormat(String $text): ?String
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', trim($text, '-'));
        $text = preg_replace('~-+~', '-', strtolower($text));
        if(empty($text)) { return null; }
        return $text;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(String $email): bool
    {
        return !!preg_match("/\S{4,}@\w{3,}\.\w{2,6}(\.\w{2})?/", $email);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword(String $pass): bool
    {
        return !!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%!^&*]).{6,20}$/", $pass);
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function validatePhone(String $phone): bool
    {
        return !!preg_match("/(\(\d{2}\)\s?)?\d{4,5}-?\d{4}/", $phone);
    }

    /**
     * @param int $size
     * @return string
     */
    public function aleatoryToken(int $size): String
    {
        $random = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $final = substr($random, 0, $size);
        return $final;
    }

    /**
     * @param $array
     * @return bool|null|array
     */
    public function fixArray($array, Bool $always = true)
    {
        return is_array($array) && !isset($array[0]) && $always ? [$array] : $array;
    }

    /**
     * @return string
     */
    public function getAdressIP() {
        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
                $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($addr[0]);
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}