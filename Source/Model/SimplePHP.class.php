<?php

namespace SimplePHP\Model;

use SimplePHP\Root\Connection;
use PDO;
use PDOException;
use SimplePHP\Model\CRUD as Actions;

/**
 * Class SimplePHP
 * @package NicollasSilva\SimplePHP
 */
class SimplePHP extends Connection {
    use Actions;

    /** @var string */
    protected $sentence = '';

    /** @var string */
    protected $offset;

    /** @var string */
    protected $order;

    /** @var string */
    protected $params = '*';

    /** @var string */
    protected $where;

    /** @var string */
    protected $limit;

    /** @var string */
    protected $table;

    /** @var object|null */
    protected $data;

    /** @var bool */
    protected $type;

    /** @var array */
    protected $excepts = [];

    /** @var string */
    protected $primary;

    /**
     * Get tablename of children model
     * @param string|null $tableName
     */
    function __construct(?string $tableName, ?string $primaryKey)
    {
        parent::__construct(); $this->table = $tableName; $this->primary = $primaryKey;
    }

    /**
     * @param int|null $id
     * @return SimplePHP
     */
    public function find(?int $id = null): SimplePHP
    {
        is_int($id) ? self::where('id', $id) : null;
        return $this;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return SimplePHP|null
     */
    public function where(string $condition, string $value): SimplePHP
    {
        $this->where = "WHERE " . (mb_strlen($this->where > 6) ? "&& {$condition} = '{$value}'" : "{$condition} = '{$value}'");
        return $this;
    }

    /**
     * @param array $params
     * @return SimplePHP|null
     */
    public function only(array $params): SimplePHP
    {
        $params !== null ? $this->params = implode($params, ',') : $this->params = '*';
        return $this;
    }

    /**
     * @param int $limit
     * @return SimplePHP|null
     */
    public function limit(int $limit): SimplePHP
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    /**
     * @param int $offset
     * @return SimplePHP|null
     */
    public function offset(int $offset): SimplePHP
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    /**
     * @param string $order
     * @return SimplePHP|null
     */
    public function orderBy(string $order): SimplePHP
    {
        $this->order = "ORDER BY {$order}";
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return string error
     */
    public function __call(string $name, $arguments)
    {
        return "This method does not exist at the SimplePHP: \"<b>{$name}</b>\".";
    }

    /**
     * @param bool $bool
     * @return SimplePHP
     */
    public function asAttribute(bool $bool = false): SimplePHP
    {
        $this->type = $bool;
        return $this;
    }

    /**
     * @param array $deniable
     * @return SimplePHP
     */
    public function except(array $deniable)
    {
        $this->excepts = $deniable;
        return $this;
    }

    /**
     * Method to destroy @except method
     */
    private function deny()
    {
        if(!empty($this->excepts)) {
            switch (!is_object($this->data) && $count = count($this->data)) {
                case (!isset($this->data[0]) && !empty($this->data)):
                    foreach($this->excepts as $except) {
                        if(isset($this->data[$except])) unset($this->data[$except]);
                    }
                    break;
                case ($count >= 2 && isset($this->data[0])):
                    foreach($this->excepts as $except) {
                        for($i = 0; $i < $count; $i++) {
                            if(isset($this->data[$i][$except])) unset($this->data[$i][$except]);
                        }
                    }
                    break;
            default:
                return [];
            }
        }
    }

    /**
     * @return array|mixed
     */
    public function execute()
    {
        try {
            $execute = $this->conn->query("SELECT {$this->params} FROM {$this->table} {$this->where} {$this->order} {$this->limit} {$this->offset}");
            $execute->rowCount() > 1 ? 
                    $this->data = ($this->type ? $execute->fetchAll(PDO::FETCH_CLASS, static::class) : $execute->fetchAll(PDO::FETCH_ASSOC)) :
                    $this->data = ($this->type ? $execute->fetchObject(static::class) : $execute->fetch(PDO::FETCH_ASSOC));
            $this->deny();
            return $this->data;
        } catch(PDOException $exc) {
            return $exc->getMessage();
        }
    }
}