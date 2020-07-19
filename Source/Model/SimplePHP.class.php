<?php

namespace Source\Model;

use Source\Root\Connection;
use PDO;
use PDOException;
use Source\Model\CRUD as Actions;

/**
 * Class SimplePHP
 * @package nicollassilva\SimplePHP
 */
class SimplePHP extends Connection {
    use Actions;

    /** @var string */
    protected $sentence = '';

    /** @var int */
    protected $offset;

    /** @var string */
    protected $order;

    /** @var string */
    protected $params = '*';

    /** @var string */
    protected $where;

    /** @var int */
    protected $limit;

    /** @var string */
    protected $table;

    /** @var object|null */
    protected $data;

    /** @var object|array */
    protected $type;

    /** @var string */
    public $excepts = [];

    /**
     * Get tablename of children model
     * @param string|null $tableName
     */
    function __construct(?string $tableName) {

        parent::__construct(); $this->table = $tableName;

    }

    /**
     * @param int|null $id
     * @return SimplePHP
     */
    public function find(?int $id = null): SimplePHP {

        is_int($id) ? self::where('id', $id) : null;
        return $this;

    }

    /**
     * @param string $condition
     * @param string $value
     * @return SimplePHP|null
     */
    public function where(string $condition, string $value): SimplePHP {

        $this->where = "WHERE " . (mb_strlen($this->where > 6) ? "&& {$condition} = '{$value}'" : "{$condition} = '{$value}'");
        return $this;

    }

    /**
     * @param array $params
     * @return SimplePHP|null
     */
    public function only(array $params): SimplePHP {

        $params !== null ? $this->params = implode($params, ',') : $this->params = '*';
        return $this;

    }

    /**
     * @param int $limit
     * @return SimplePHP|null
     */
    public function limit(int $limit): SimplePHP {
        
        $this->limit = is_int($limit) ? "LIMIT $limit" : null;
        return $this;

    }

    /**
     * @param int $offset
     * @return SimplePHP|null
     */
    public function offset(int $offset): SimplePHP {
        
        $this->offset = is_int($offset) ? "OFFSET $offset" : null;
        return $this;

    }

    /**
     * @param string $order
     * @return SimplePHP|null
     */
    public function orderBy(string $order): SimplePHP {
        
        $this->order = "ORDER BY {$order}";
        return $this;

    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return string error
     */
    public function __call(string $name, $arguments) {

        return "This method does not exist at the SimplePHP: \"<b>{$name}</b>\".";

    }

    /**
     * @param bool $bool
     * @return SimplePHP
     */
    public function asAttribute(bool $bool = false): SimplePHP {

        $this->type = $bool;
        return $this;

    }

    /**
     * @param array $deniable
     * @return SimplePHP
     */
    public function except(array $deniable) {

            $this->excepts = $deniable;

        return $this;

    }

    /**
     * Method to destroy @except method
     */
    private function deny() {

        if(!empty($this->excepts)) {
            foreach($this->excepts as $except) {
                if(isset($this->data[$except])) unset($this->data[$except]);
            }
        }

    }

    /**
     * @return array|object|null
     */
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