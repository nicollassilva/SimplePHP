<?php

namespace SimplePHP\Traits;

trait Properties {
    public function getTable()
    {
        return isset($this->table) && gettype($this->table) == 'string'
            ? $this->table : null;
    }

    public function getPrimary()
    {
        return isset($this->primaryKey) && gettype($this->primaryKey) == 'string'
            ? $this->primaryKey : null;
    }

    public static function find() {
        
    }
}