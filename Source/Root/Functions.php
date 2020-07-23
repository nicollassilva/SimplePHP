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
        $random = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz+-&%#@()ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $final = substr($random, 0, $size);
        return $final;
    }
}