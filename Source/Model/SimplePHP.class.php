<?php

namespace Source\Model;

use Source\Root\Connection;
use PDO;
use PDOException;
use Source\Model\CRUD as Actions;

/**
 * Class SimplePHP
 * @package NicollasSilva\SimplePHP
 */
class SimplePHP extends Connection {
    use Actions;

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

    /** @var dataFetch */
    protected $data;

    /** @var dataFetchType */
    protected $type;

    /** @var exceptsParamsToQuery */
    public $excepts = [];

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

    public function only(array $params): SimplePHP {

        $params !== null ? $this->params = implode($params, ',') : $this->params = '*';
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

    public function asAttribute(bool $bool = false): SimplePHP {

        $this->type = $bool;
        return $this;

    }

    public function except(array $deniable) {

            $this->excepts = $deniable;

        return $this;

    }

    private function deny() {

        if(!empty($this->excepts)) {
            foreach($this->excepts as $except) {
                if(isset($this->data[$except])) unset($this->data[$except]);
            }
        }

    }

    public function execute() {

        try {
            $execute = $this->conn->query("SELECT {$this->params} FROM {$this->table} {$this->where} {$this->order} {$this->limit} {$this->offset}");
            $execute->rowCount() > 1 ? 
                    $this->data = ($this->type ? $execute->fetchAll(PDO::FETCH_CLASS, static::class) : $execute->fetchAll(PDO::FETCH_ASSOC)) :
                    $this->data = ($this->type ? $execute->fetchObject(static::class) : $execute->fetch(PDO::FETCH_ASSOC));
            self::deny();
            return $this->data;
        } catch(PDOException $exc) {
            return $exc->getMessage();
        }

    }

    public function save() {

        

    }

}