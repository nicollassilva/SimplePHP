# Simple PHP - Simple and easy CRUD for PHP projects

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/?branch=master)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/nicollassilva/simplephp.svg?style=flat-square)](https://packagist.org/packages/nicollassilva/simplephp)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Version](https://img.shields.io/github/release/nicollassilva/simplephp.svg?style=flat-square)](https://github.com/nicollassilva/simplephp/releases)
[![Build Status](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/nicollassilva/simplephp.svg?style=flat-square)](https://packagist.org/packages/nicollassilva/simplephp)

A small CRUD project developed to assist daily routines and speed up the process of communicating with the database with security and transparency.

## Getting Started

Some precautions and step-by-step instructions for you to download and install the package.

### Prerequisites

To be able to use **SimplePHP** you will need to have:

```
PHP **^7.2.5**
EXT-PDO **ANY**
```

### Installing

SimplePHP can be installed via **composer.json** or via the **command terminal**:

```
composer require nicollassilva/simplephp dev-master
```

or **composer.json**:

```
"nicollassilva/simplephp": "dev-master"
```

## Documentation

### Connection

To configure the connection to the database, you must access: **Source\Root\Config.php**.
Example of the file to be found:

```php
protected $config = [
        "driver" => "mysql",
        "hostname" => "localhost",
        "charset" => "utf8",
        "port" => 3306,
        "username" => "root",
        "password" => "",
        "database" => "marketplace",
        "options" => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        ]
    ];
```

### Example Model

You will need to inherit your model from the SimplePHP class, and in the magic constructor method, call the parent constructor with the name of the table for the referred model.

```php
namespace Model;

use SimplePHP\Model\SimplePHP;

class User extends SimplePHP {

    function __construct() {
    
        parent::__construct('users');
        
    }
    
}
```

### Methods

#### find

```php

use Models\User;

$user = new User();

/** find all users */
$user = $find->find()->execute();

/** find user by id */
$user = $find->find(5)->execute();

/** find user with one condition */
$user = $find->find()->where('email', 'example@gmail.com')->execute();

/** find user with + conditions */
$user = $find->find()->where('name', 'Nícollas Silva')->where('email', 'nicollas@gmail.com')->execute();

/** find users with limit */
$user = $find->find()->limit(5)->execute();

/** find users with limit & offset */
$user = $find->find()->limit(5)->offset(5)->execute();

/** find users with orderBy */
$user = $find->find()->orderBy('id ASC')->execute();

/** find users and return results as attributes. [| Example: $user->name instead of $user['name'] |] */
$user = $find->find()->asAttribute(true)->execute(); 

/** find users with specific columns. */
$user = $find->find()->only(['name', 'id', 'email'])->execute();

/** find users creating exceptions in columns. */
$user = $find->find()->except(['password'])->execute();

```

* **Note:** _except()_ method does not work chained with the _asAttribute(true)_ method 

## Authors

* **Nícollas Silva** - *Developer* - [NicollasSilva](https://github.com/nicollassilva)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
