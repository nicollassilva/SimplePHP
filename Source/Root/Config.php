<?php

namespace SimplePHP\Root;

use PDO;

/**
 * Trait Config of SimplePHP
 * EN: Modify these settings according to your database data.
 * PT/BR: Modifique essas configurações de acordo com os dados do banco de dados.
 * @package NicollasSilva/SimplePHP
 */
trait Config {

    /** @return ConfigDatabase */
    protected $config = [
        "driver" => "mysql",
        "hostname" => "localhost",
        "charset" => "utf8",
        "port" => 3306,
        "username" => "root",
        "password" => "",
        "database" => "marketplace",
        "options" => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        ]
    ];

}