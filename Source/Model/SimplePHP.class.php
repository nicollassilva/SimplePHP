<?php

namespace SimplePHP\Model;

use SimplePHP\Root\Connection;
use PDO;
use PDOException;
use Error;
use stdClass;
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

    /** @var array */
    protected $request = [];

    /**
     * Get tablename of children model
     * @param string|null $tableName
     */
    function __construct(String $tableName, String $primaryKey)
    {
        parent::__construct(); $this->table = $tableName; $this->primary = $primaryKey;
    }

    /**
     * @param int|null $id
     * @return SimplePHP|null
     */
    public function find(int $id = null): ?SimplePHP
    {
        is_int($id) ? $this->where('id', $id) : null;
        return $this;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return SimplePHP|null
     */
    public function where(String $condition, String $value): ?SimplePHP
    {
        $this->where = "WHERE " . (mb_strlen($this->where > 6) ? "&& {$condition} = '{$value}'" : "{$condition} = '{$value}'");
        return $this;
    }

    /**
     * @param array $params
     * @return SimplePHP|null
     */
    public function only(Array $params): ?SimplePHP
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param int $limit
     * @return SimplePHP|null
     */
    public function limit(int $limit): ?SimplePHP
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    /**
     * @param int $offset
     * @return SimplePHP|null
     */
    public function offset(int $offset): ?SimplePHP
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    /**
     * @param string $order
     * @return SimplePHP|null
     */
    public function orderBy(String $order): ?SimplePHP
    {
        $this->order = "ORDER BY {$order}";
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return string error
     */
    public function __call(String $name, $arguments)
    {
        return "This method does not exist at the SimplePHP: \"<b>{$name}</b>\".";
    }

    /**
     * @param array $deniable
     * @return SimplePHP|null
     */
    public function except(Array $deniable)
    {
        $this->excepts = $deniable;
        return $this;
    }

    /**
     * @param $prop
     * @param $value
     * @return null
     */
    public function __set($prop, $value)
    {
        if (empty($this->data)) {
            $this->data = new stdClass();
        }

        $this->data->$prop = $value;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function __isset($attribute): bool
    {
        return isset($this->data->$attribute);
    }

    /**
     * @param $prop
     * @return string|null
     */
    public function __get($prop)
    {
        return $this->data->$prop ?? null;
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
                        if(isset($this->data[$except])) {
                            unset($this->data[$except]);
                        }
                    }
                    break;
                case ($count >= 2 && isset($this->data[0])):
                    foreach($this->excepts as $except) {
                        for($i = 0; $i < $count; $i++) {
                            if(isset($this->data[$i][$except])) {
                                unset($this->data[$i][$except]);
                            }
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
    public function execute(bool $type = false)
    {
        $this->type = $type;
        try {
            $execute = $this->conn->query("SELECT {$this->params} FROM {$this->table} {$this->where} {$this->order} {$this->limit} {$this->offset}");
            $execute->rowCount() > 1 ? 
                    $this->data = ($this->type ? $execute->fetchAll(PDO::FETCH_CLASS, static::class) : $execute->fetchAll(PDO::FETCH_ASSOC)) : $this->data = ($this->type ? $execute->fetchObject(static::class) : $execute->fetch(PDO::FETCH_ASSOC));
            $this->deny();
            return $this->data;
        } catch (PDOException $exc) {
            return $exc->getMessage();
        }
    }

    /**
     * @return \Exception|bool(true)
     */
    public function destroy()
    {
        $primary = $this->primary;
        if (!isset($this->data->$primary)) {
            $this->error("Índice primário não encontrado: {$primary}.", __FUNCTION__);
        }

        return $this->delete($this->data->$primary);
    }

    /**
     * @return Error|PDOException|bool
     */
    public function save()
    {
        $primary = $this->primary;
        $data = json_decode(json_encode($this->data), true);
        if (empty($primary) || !isset($data[$primary])) {
            $this->error("Índice primário não encontrado: {$primary}.", __FUNCTION__);
        } else if(!$this->find($data[$primary])->execute()) {
            $this->error("Esse registro não consta no banco de dados: {$data[$primary]}.", __FUNCTION__);
        }

        $otherPrimary = $data[$primary];
        unset($data[$primary]);
        $parameters = implode(',', array_keys($data));
        $values = array_values($data);

        return $this->update($parameters, $values, $otherPrimary);
    }

    /**
     * @return SimplePHP|null
     */
    public function request(Array $request): ?SimplePHP
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return PDOException|Error|bool
     */
    public function create()
    {
        $request = $this->request;
        if(empty($request)) {
            $this->error("O array request está vazio!", __FUNCTION__);
        }

        $parameters = implode(',', array_keys($request));
        $values = array_values($request);
        
        return $this->insert($parameters, $values);
    }

    /**
     * @param string $message
     * @param string $function
     * @return Error|null
     */
    public function error(String $message, String $function): ?Error {
        if($message) { throw new Error($message." Método: ".strtoupper($function)); } else { return null; };
    }
}