<?php

namespace Model;

use Source\Model\SimplePHP;

class User extends SimplePHP {

    function __construct() {

        parent::__construct('users');

    }

}