<?php

namespace SimplePHP\Model;

trait CRUD {

    /**
     * @param int $primary
     * @return bool
     */
    public function delete(int $primary): ?bool
    {
        try {
            $sql = $this->conn->prepare("DELETE FROM {$this->table} WHERE {$this->primary} = :primary");
            $sql->bindParam(':primary', $primary);
            return $sql->execute();
        } catch(PDOException $exception) {
            return null;
        }
    }

    /**
     * @param string $params
     * @param array $values
     * @param int $primary
     * @return bool
     */
    public function update(String $params, Array $values, Int $primary): bool
    {
        $params = explode(',', $params);
        $data = [];
        $countParams = count($params);
        for($i = 0; $i < $countParams; $i++) {
            $data[$i] = ":".$params[$i][0].$params[$i][1].$params[$i][2].", ";
        }
        $result = '';
        $final = array_map(null, $params, $data);
        foreach($final as $key => $vals) {
            foreach($vals as $chave => $val) {
                $result .= str_replace(':', ' = :', $val);
            }
        }
        $result = rtrim($result, ', ');
        $sql = $this->conn->prepare("UPDATE {$this->table} SET {$result} WHERE {$this->primary} = '{$primary}'");
        for($i = 0; $i < $countParams; $i++) {
            $data[$i] = ":".$params[$i][0].$params[$i][1].$params[$i][2];
        }
        $countData = count($data);
        for($i = 0; $i < $countData; $i++) {
            $sql->bindParam($data[$i], $values[$i]);
        }
        if($sql->execute()) {
            return true;
        } else {
            return false;
        }
    }
}