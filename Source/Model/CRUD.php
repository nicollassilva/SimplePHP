<?php

namespace SimplePHP\Model;

trait CRUD {
    /**
     * @param int $primary
     * @return null|bool
     */
    public function delete(int $primary): ?bool
    {
        try {
            $sql = $this->conn->prepare("DELETE FROM {$this->table} WHERE {$this->primary} = :primary");
            $sql->bindParam(':primary', $primary);
            return $sql->execute();
        } catch (PDOException $exception) {
            return null;
        }
    }
}