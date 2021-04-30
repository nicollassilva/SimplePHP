<?php

namespace SimplePHP\Root;

use Exception;
use PDO;
use PDOException;

/**
 * Class Connection
 * @package NicollasSilva\SimplePHP
 */

abstract class Connection {

    /** @var object|null */
    protected static $conn;

    /** @var array */
    protected static $config;
    
    /**
     * Connection construtor
     */
    public static function getConnection() {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 5) . '/App/Boot/');
        $dotenv->load();
        self::$config = [
            'driver' => $_ENV['DB_CONNECTION'],
            'hostname' => $_ENV['DB_HOST'],
            'charset' => $_ENV['DB_CHARSET'],
            'port' => $_ENV['DB_PORT'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'timezone' => $_ENV['DB_TIMEZONE'],
            'pathLog' => self::getRealDirectory() . DIRECTORY_SEPARATOR . $_ENV['PATH_LOG'] . DIRECTORY_SEPARATOR . 'Logs.log',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
            ]
        ];
        if(empty(self::$conn)) {
            self::connectPdo();
        }

        return self::$conn;
    }

    /**
     * Function connect PDO
     */
    public static function connectPdo()
    {
        date_default_timezone_set(self::$config['timezone']);
        
        if(empty(self::$conn)) {
            try {
                self::$conn = new PDO(
                    self::$config['driver'] . ":host=" .
                    self::$config['hostname'] . ";charset=" .
                    self::$config['charset'] . ";port=" .
                    self::$config['port'] . ";dbname=" .
                    self::$config['database'],
                    self::$config['username'],
                    self::$config['password'],
                    self::$config['options']
                );
            } catch(PDOException $exception) {
                return false;
            }
        }
    }

    public static function getRealDirectory()
    {
        $realDirectory = dirname(__DIR__, 5);
        $completeDirectoryToLog = $realDirectory . DIRECTORY_SEPARATOR . $_ENV['PATH_LOG'];

        if(!file_exists($completeDirectoryToLog . 'Logs.log')) {
            try {
                fopen($completeDirectoryToLog . 'Logs.log', 'a');
            } catch(Exception $error) {
                return false;
            }
        }

        return $realDirectory;
    }
    
    public static function find() {}
}