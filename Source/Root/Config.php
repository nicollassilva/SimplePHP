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

    /** @var array $config */
    protected $config = [
        "driver" => "mysql",
        "hostname" => "localhost",
        "charset" => "utf8mb4",
        "port" => 3306,
        "username" => "root",
        "password" => "",
        "database" => "",
        "timezone" => "America/Sao_Paulo",
        "pathLog" => __DIR__ . "/../../../../../your-log.log",
        "options" => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        ]
    ];
}