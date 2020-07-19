<?php

namespace Source\Model;

use Source\Root\Connection;
use PDO;

/**
 * Class SimplePHP
 * @package NicollasSilva\SimplePHP
 */
class SimplePHP extends Connection {

    /** @return completeQuery */
    protected $sentence = '';

    /** @var offsetToQuery */
    protected $offset;

    /** @var orderByToQuery */
    protected $order;

    /** @var paramsToQuery */
    protected $params = '*';

    /** @var whereToQuery */
    protected $where;

    /** @var limitToQuery */
    protected $limit;

    /** @return tableToDatabase */
    protected $table;

    /** 
     * @return attributesValue
     */
    function __construct(?string $tableName) {

        parent::__construct(); $this->table = $tableName;

    }

    public function find(?int $id = null): SimplePHP {

        is_int($id) ? self::where('id', $id) : null;
        return $this;

    }

    public function where($condition, $value): SimplePHP {

        $this->where = "WHERE " . (mb_strlen($this->where > 6) ? "&& {$condition} = '{$value}'" : "{$condition} = '{$value}'");
        return $this;

    }

    public function limit(int $limit): SimplePHP {
        
        $this->limit = is_int($limit) ? "LIMIT $limit" : null;
        return $this;

    }

    public function offset(int $offset): SimplePHP {
        
        $this->offset = is_int($offset) ? "OFFSET $offset" : null;
        return $this;

    }

    public function orderBy(string $order): SimplePHP {
        
        $this->order = "ORDER BY {$order}";
        return $this;

    }

    public function __call($name, $arguments) {

        echo "This method does not exist at the SimplePHP: \"<b>{$name}</b>\".";
        exit();

    }

    public function execute() {

        $execute = $this->conn->query("SELECT {$this->params} FROM {$this->table} {$this->where} {$this->order} {$this->limit} {$this->offset}");
        return $execute->rowCount() > 1 ? 
                $execute->fetchAll(PDO::FETCH_ASSOC) :
                $execute->fetch(PDO::FETCH_ASSOC);

    }

}