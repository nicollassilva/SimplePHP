<?php

namespace Source\Root;

use PDO;
use PDOException;

/**
 * Class Connection
 * @package NicollasSilva\SimplePHP
 */

class Connection {
    use Config;

    /** @var PDO */
    protected $conn;

    /** @var PDOException */
    protected $exception;

    /** @var TableName */
    protected $table;
    
    /**
     * @param ExecutePDO
     */
    function __construct() { self::PDO(); }

    /**
     * @return PDOConnection
     */
    public function PDO() {

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