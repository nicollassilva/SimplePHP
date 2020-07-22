<?php

namespace SimplePHP\Root;

use PDO;
use PDOException;

/**
 * Class Connection
 * @package NicollasSilva\SimplePHP
 */

class Connection {
    use Config;

    /** @var object|null */
    protected $conn;

    /** @var array */
    protected $exception;

    /** @var string */
    protected $table;
    
    /**
     * Connection construtor
     */
    function __construct() { $this->PDO(); }

    /**
     * Function connect PDO
     */
    public function PDO()
    {
        date_default_timezone_set($this->config['timezone']);
        
        if(empty($this->conn)) {
            try {
                $this->conn = new PDO(
                    $this->config['driver'] . ":host=" .
                    $this->config['hostname'] . ";charset=" .
                    $this->config['charset'] . ";port=" .
                    $this->config['port'] . ";dbname=" .
                    $this->config['database'],
                    $this->config['username'],
                    $this->config['password'],
                    $this->config['options']
                );
            } catch(PDOException $exception) {
                echo json_encode(
                    ['error' => 
                        [
                            'message' => $exception->getMessage()
                        ]
                ]);
            }
        }
    }
}