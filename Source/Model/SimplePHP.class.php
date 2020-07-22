<?php

namespace SimplePHP\Model;

use SimplePHP\Root\Connection;
use PDO;
use PDOException;
use Exception;
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

    /** @var string */
    protected $primary;

    /** @var array */
    protected $request = [];

    /** @var array */
    protected $excepts = [];

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
        $this->params = implode($params, ',');
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
     * @return null
     */
    public function __call(String $name, $arguments)
    {
        return writeLog("This method does not exist at the SimplePHP: \"<b>{$name}</b>\".");
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
     * @return null
     */
    private function deny()
    {
        if (!empty($this->excepts)) {
            foreach ($this->excepts as $except) {
                if (isset($this->data[$except])) unset($this->data[$except]);
            }
        }
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
            return writeLog($exc->getCode(), true);
        }
    }

    /**
     * @return null|bool
     */
    public function destroy()
    {
        $primary = $this->primary;
        if (!isset($this->data->$primary)) {
            return $this->writeLog("The primary index was not found.");
        }

        return $this->delete($this->data->$primary);
    }

    /**
     * @return null|bool
     */
    public function save()
    {
        $primary = $this->primary;
        $data = json_decode(json_encode($this->data), true);
        if (empty($primary) || !isset($data[$primary])) {
            return $this->writeLog("The primary index was not found.");
        } else if (!$this->find($data[$primary])->execute()) {
            return $this->writeLog("The primary index was not found in the database.");
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
     * @return null|bool
     */
    public function create()
    {
        $request = $this->request;
        if (empty($request)) {
            return writeLog("No information was passed to record.");
        }

        $parameters = implode(',', array_keys($request));
        $values = array_values($request);
        
        return $this->insert($parameters, $values);
    }

    /**
     * @param string $message
     * @return null
     */
    protected function writeLog($message, $pdo = false)
    {
        $message = $pdo ? "Error: PDOCode " . $message : $message;
        $archive = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . "Logs" . DIRECTORY_SEPARATOR . "Logs.txt", 'a+');
        fwrite($archive, "-----SimplePHPLog-----\n" . date("d/m/Y H:i:s", time()) . " -> ". $message ."\n-------\n");
        fclose($archive);
        return null;
    }
}