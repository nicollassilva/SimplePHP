<?php

namespace SimplePHP\Model;

use PDOException;
use SimplePHP\Root\Connection;

/**
 * Trait CRUD to SimplePHP
 * @package NicollasSilva\SimplePHP
 */
trait CRUD
{

    /**
     * @param int $primary
     * @return bool|null
     */
    public function delete(int $primary): ?bool
    {
        try {
            $sql = Connection::getConnection()->prepare("DELETE FROM {$this->table} WHERE {$this->primary} = :primary");
            $sql->bindParam(':primary', $primary);
            return $sql->execute();
        } catch (PDOException $exception) {
            return $this->writeLog($exception->getMessage(), true);
        }
    }

    /**
     * @param string $params
     * @param array $values
     * @param int $primary
     * @return bool|null
     */
    public function update(String $params, array $values, Int $primary)
    {
        try {
            $params = explode(',', $params);
            $data = [];
            $countParams = count($params);
            for ($i = 0; $i < $countParams; $i++) {
                $data[$i] = ":" . $params[$i] . $i . ", ";
            }
            $result = '';
            $final = array_map(null, $params, $data);
            foreach ($final as $key => $vals) {
                foreach ($vals as $chave => $val) {
                    $result .= str_replace(':', ' = :', $val);
                }
            }
            $result = rtrim($result, ', ');
            $sql = Connection::getConnection()->prepare("UPDATE {$this->table} SET {$result} WHERE {$this->primary} = '{$primary}'");
            for ($i = 0; $i < $countParams; $i++) {
                $data[$i] = ":" . $params[$i] . $i;
            }
            $countData = count($data);
            for ($i = 0; $i < $countData; $i++) {
                $sql->bindParam($data[$i], $values[$i]);
            }
            return $sql->execute();
        } catch (PDOException $exception) {
            return $this->writeLog($exception->getMessage(), true);
        }
    }

    /**
     * @param string $params
     * @param array $values
     * @return bool|null
     */
    public function insert(String $params, array $values)
    {
        try {
            $parameters = "(" . $params . ")";
            $params = explode(',', $params);
            $data = [];
            $countParams = count($params);
            for ($i = 0; $i < $countParams; $i++) {
                $data[$i] = ":" . $params[$i] . $i;
            }
            $valueBind = "(" . implode(', ', $data) . ")";
            $sql = Connection::getConnection()->prepare("INSERT INTO {$this->table} $parameters VALUES $valueBind");
            for ($i = 0; $i < $countParams; $i++) {
                $sql->bindParam($data[$i], $values[$i]);
            }
            return $sql->execute();
        } catch (PDOException $exception) {
            return $this->writeLog($exception->getMessage(), true);
        }
    }
}
