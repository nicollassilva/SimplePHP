<?php

namespace SimplePHP\Model;

use SimplePHP\Root\{
    Connection,
    Functions
};
use SimplePHP\Traits\Properties;
use SimplePHP\Model\ChildSimplePHP;
use PDO,
    PDOException,
    stdClass;
use SimplePHP\Model\CRUD as Actions;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class SimplePHP
 * @package NicollasSilva\SimplePHP
 */
class SimplePHP extends Connection {
    use Actions, Functions, Properties;

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

    /** @var bool */
    protected $count = false;

    /** @var string */
    protected $group;

    /** @var object|null */
    protected $childClass;

    /** @var array */
    private static $reservedWords = ['where', 'find', 'by'];

    /**
     * Get tablename of children model
     * @param string|null $tableName
     */
    function __construct(String $tableName = null, String $primaryKey = null)
    {
        $this->setRealTable()->setRealPrimary();

        $this->childClass = new ChildSimplePHP(get_called_class());

        if($tableName) $this->table = $tableName;

        if($primaryKey) $this->primary = $primaryKey;
    }

    private function setRealTable() : ?SimplePHP
    {
        $this->table = $this->getTable() ?? strtolower($this->childClass->realName) . 's';
        return $this;
    }

    private function setRealPrimary() : ?SimplePHP
    {
        $this->primary = $this->getPrimary() ?? 'id';
        return $this;
    }

    /**
     * @param int|null $id
     * @return SimplePHP|null
     */
    public function find(int $id = null): ?SimplePHP
    {
        is_int($id) ? $this->where([[$this->primary, '=', $id]]) : null;

        return $this;
    }

    /**
     * @param array $where
     * @param string $condition = 'AND'
     * @return SimplePHP|null
     */
    public function where(Array $where, String $condition = 'AND'): ?SimplePHP
    {
        $this->where = '';
        foreach($where as $enclosures) {
            $split = isset($enclosures[3]) && !$enclosures[3] ? $enclosures[2] : "'".$enclosures[2]."'";
            $this->where .= $enclosures[0]." ".$enclosures[1]." ".$split." {$condition} ";
        }
        $this->where = "WHERE " . rtrim($this->where, " {$condition} ");

        return $this;
    }

    /**
     * @param array $where
     * @return SimplePHP|null
     */
    public function whereRaw(String $where): ?SimplePHP
    {
        $this->where = "WHERE " . $where;
        return $this;
    }

    /**
     * @param array $orWhere
     * @param string $condition = 'AND'
     * @return SimplePHP|null
     */
    public function orWhere(Array $orWhere, String $condition = 'AND'): ?SimplePHP
    {
        $moreWhere = '';
        foreach($orWhere as $enclosures) {
            $split = isset($enclosures[3]) && !$enclosures[3] ? $enclosures[2] : "'".$enclosures[2]."'";
            $moreWhere .= $enclosures[0]." ".$enclosures[1]." ".$split." {$condition} ";
        }
        $this->where .= " OR " . rtrim($moreWhere, " {$condition} ");

        return $this;
    }

    /**
     * @param array $params
     * @return SimplePHP|null
     */
    public function only(Array $params): ?SimplePHP
    {
        $this->params = implode(',', $params);
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
     * @param string $ordenation = 'ASC'
     * @return SimplePHP|null
     */
    public function orderBy(String $prop, String $ordenation = 'ASC'): ?SimplePHP
    {
        if(mb_strlen($this->order) < 9) {
            $this->order = "ORDER BY {$prop} {$ordenation}";
        } else {
            $this->order .= ", {$prop} {$ordenation}";
        }
        return $this;
    }

    /**
     * @param string $prop
     * @return SimplePHP|null
     */
    public function groupBy(String $prop): ?SimplePHP
    {
        if(mb_strlen($this->group) < 9) {
            $this->group = "GROUP BY {$prop}";
        } else {
            $this->group .= ", {$prop}";
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return Array|Null|SimplePHP
     */
    public function __call(String $name, $arguments)
    {
        if($name === 'skip')
            return $this->offset($arguments[0]);

        if($name === 'take')
            return $this->limit($arguments[0]);

        if($name === 'get')
            return $this->execute();

        if($name === 'first')
            return $this->execute(true);

        return $this->writeLog("This method does not exist at the SimplePHP: \"<b>{$name}</b>\".");
    }

    /**
     * @param array $deniable
     * @return SimplePHP|null
     */
    public function except(Array $deniable): ?SimplePHP
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
    public function __get($prop): ?String
    {
        return $this->data->$prop ?? null;
    }

    public function count(): SimplePHP
    {
        $this->count = true;
        return $this;
    }

    /**
     * @return array|object|int|null
     */
    public function execute(bool $type = false)
    {
        $this->type = $type;
        try {
            if(!is_object(Connection::getConnection())) 
                return $this->writeLog("Connection failed. Check your connection config and try again.", true);
            
            $execute = Connection::getConnection()->query("SELECT {$this->params} FROM {$this->table} {$this->where} {$this->group} {$this->order} {$this->limit} {$this->offset}");
            $execute->rowCount() > 1 ? 
                    $this->data = ($this->type ? $execute->fetchAll(PDO::FETCH_CLASS, static::class) : $execute->fetchAll(PDO::FETCH_ASSOC)) : $this->data = ($this->type ? $execute->fetchObject(static::class) : $execute->fetch(PDO::FETCH_ASSOC));
        $this->deny();
        return !$this->count ? $this->data : $execute->rowCount();
        } catch (PDOException $exc) {
            return $this->writeLog($exc->getMessage(), true);
        }
    }

    /**
     * @return null|bool
     */
    public function destroy(): ?bool
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
    public function create(): ?bool
    {
        $request = $this->request;
        if (empty($request)) {
            return $this->writeLog("No information was passed to record.");
        }

        $parameters = implode(',', array_keys($request));
        $values = array_values($request);
        
        return $this->insert($parameters, $values);
    }

    /**
     * @param string $message
     * @return null|Logger
     */
    protected function writeLog($message, $pdo = false): void
    {
        $log = new Logger('SimplePHP');
        $log->pushHandler(new StreamHandler(self::$config["pathLog"], Logger::WARNING));

        if($pdo) {
            $log->error($message);
            return;
        }

        $log->warning($message);
    }

    /**
     * @param string $table
     * @return SimplePHP|null
     */
    public function useTable(String $table): ?SimplePHP
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function debugQuery(): String
    {
        return "SELECT {$this->params} FROM {$this->table} {$this->where} {$this->group} {$this->order} {$this->limit} {$this->offset}";
    }

    /**
     * @return SimplePHP|null
     */
    public static function __callStatic($name, $arguments)
    {
        // if($name === 'find')
        //     return (new Static)->find($arguments[0])->execute(true);
    }

    /**
     * @return SimplePHP|null
     */
    public static function findByPrimary(Int $id) : ?SimplePHP
    {
        return (
            new static()
        )->find($id);
    }
}