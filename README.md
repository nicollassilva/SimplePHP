# Simple PHP - Simple and easy QueryBuilder for PHP projects

[![Maintainer](http://img.shields.io/badge/maintainer-@nicollassilva-blue.svg?style=flat-square)](https://github.com/nicollassilva)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/?branch=master)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/nicollassilva/simplephp.svg?style=flat-square)](https://packagist.org/packages/nicollassilva/simplephp)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Version](https://img.shields.io/github/release/nicollassilva/simplephp.svg?style=flat-square)](https://github.com/nicollassilva/simplephp/releases)
[![Build Status](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/nicollassilva/SimplePHP/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/nicollassilva/simplephp.svg?style=flat-square)](https://packagist.org/packages/nicollassilva/simplephp)

###### Um pequeno projeto construtor de query desenvolvido para auxiliar as rotinas diárias e acelerar o processo de comunicação com o banco de dados com segurança e transparência.
A small query builder project developed to assist daily routines and speed up the process of communicating with the database with security and transparency.

![Logomarca](logo.png)

## Getting Started

Some precautions and step-by-step instructions for you to download and install the package.

### Prerequisites

To be able to use **SimplePHP** you will need to have:

```
PHP ^7.2.5
EXT-PDO *
```

### Installing

SimplePHP can be installed via **composer.json** or via the **command terminal**:

```
composer require nicollassilva/simplephp
```

or **composer.json**:

```
"nicollassilva/simplephp": "^1.7"
```

#### Connection

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
        "database" => "",
        "timezone" => "America/Sao_Paulo",
        "options" => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
        ]
    ];
```

#### Example of your model

After completing the database configuration, create a folder at the root of the project where your **Models** will be and create the class. 
* You should extend and use the SimplePHP class namespace, as in the example:

```php
namespace Models;

use SimplePHP\Model\SimplePHP;

class User extends SimplePHP {

    function __construct()
    {
        /**
         * @param string Table Name
         * @param string Primary Key
         */
        parent::__construct('users', 'id');
    }
}
```

* You will need to inherit your model from the SimplePHP class, and in the magic constructor method, call the parent constructor with the name of the table for the referred model, and a primary key.

#### First use

After all the previous steps, create an index.php at the root of the project giving a **require in the composer autoload and in your model class**, after that, instantiate your model and you can start using SimplePHP. Below is an example:

```php
    require "vendor/autoload.php";
    require "models/user.php";

use Models\User;

    $userModel = new User();
    $user = $userModel->find()->execute();
```

### Installation Errors

#### Some mistakes and how to fix them

> Fatal error: Uncaught Error: Class 'SimplePHP\Model\SimplePHP' not found

To fix it, execute the following command in the project's root folder:

```
composer dump -o
```

## Documentation

### Methods

#### find

```php

use Models\User;

$userModel = new User();

/** find all users */
$user = $userModel->find()->execute();

/** find user by id */
$user = $userModel->find(5)->execute();

/** find users and return the total result count */
$count = $userModel->find()->count()->execute();

/** find user with one where */
$user = $userModel->find()->where([
                                     ['name', '=', 'Nicollas']
                                ])->execute();

/** find user with several where. Conditional AND */
$user = $userModel->find()->where([
                                     ['name', '=', 'John'],
                                     ['email', '=', 'johnmoppans@gmail.com']
                                ])->execute();

/** find user with LIKE. Conditional AND */
$user = $userModel->find()->where([
                                     ['name', 'LIKE', '%Guilherme%'],
                                     ['email', '=', 'guilherme@gmail.com']
                                ])->execute();

/** find user with conditional where. Condicional OR */
$user = $userModel->find()->where([
                                     ['name', 'LIKE', '%Nicollas%'],
                                     ['name', 'LIKE', '%Nicolas%']
                                ], 'OR')->execute();

/** find users with limit */
$user = $userModel->find()->limit(5)->execute();

/** find users with limit & offset */
$user = $userModel->find()->limit(5)->offset(5)->execute();

/** find users with orderBy. second parameter optional, default ASC */
$user = $userModel->find()->orderBy('id', 'DESC')->orderBy('name')->execute();

/** find users and return results as attributes. EXAMPLE: $user->name instead of $user['name'] */
$user = $userModel->find()->execute(true); 

/** find users with specific columns. */
$user = $userModel->find()->only(['name', 'id', 'email'])->execute();

/** find users creating exceptions in columns. */
$user = $userModel->find(5)->except(['password'])->execute();

```

* **Note:** _except()_ method does not work chained with the _execute(true)_ method, only _execute()_ without parameter true.
* **Note:** _except()_ method only works when looking for specific information, in multidimensional arrays it does not work. This will be fixed soon.

#### destroy

```php
use Models\User;

$userModel = new User();
$user = $userModel->find(3)->execute(true);

    /** @return null|bool */
    if($user->destroy()) {
        echo "Success delete!";
    }
```

* **Note:** To delete an information, you need to be aware that there is a reference to that information, that is, the primary key.

#### save (update)

```php
use Models\User;

$userModel = new User();
$user = $userModel->find(5)->execute(true);
$user->name = "Other name";
$user->email = "anyemail@gmail.com";

    /** @return null|bool */
    if($user->save()) {
        echo "Success!";
    }
```

* **Note:** To save an information, you need to be aware that there is a reference to that information, that is, the primary key.
* **OBS:** You can use the only() method to pull only the necessary information, but when editing, you can pass any column that exists in the database table and the system will proceed to treat and insert it. Example:

```php
use Models\User;

$userModel = new User();
$user = $userModel->find(8)->only(['id', 'name'])->execute(true);
$user->name = "Russian Gabolev";

$user->email = "anyemail@gmail.com";
/** This informations was not called from the database, but they exist. */
$user->updated_at = time();

    /** @return null|bool */
    if($user->save()) {
        echo "Success!";
    }
```
* **OBS2:** In case of failure, it will return NULL, if completed, it will return true.

#### create (insert)

```php
use Models\User;

    $userModel = new User();
    $user = $userModel->request([
        "name" => "Dr. Haylie Bahringer",
        "email" => 'hayliebahringer@gmail.com', 
        "password" => 123456 // Encrypt before sending to the database
    ])->create();
```

It is also possible by passing a direct array:

```php
use Models\User;

    $userModel = new User();
    $_POST = [
        "name" => "Hadjei Moccab",
        "email" => "hadjeiofficial@gmail.com",
        "password" => 123456 // Encrypt before sending to the database
    ];
    $user = $userModel->request($_POST)->create();
```

* **OBS2:** In case of failure, it will return NULL, if completed, it will return true.

### Others methods

#### 
```php
use Models\User;

    $userModel = new User();
    $user = $userModel->find(18)->execute(true);
    
    /** @return string | @param string $hash, @param Array $options */
    $user->password = $userModel->hashFormat($_POST['password']);
    
    /** @return string | @param string $url */
    $user->home = $userModel->urlFormat('Dr. John,Sik!@'); // dr-john-sik
    
    /** @return bool | @param string $email */
    if(!$userModel->validateEmail($_POST['email'])) {
        echo "Invalid email!";
    }
    
    /** @return bool | @param string $pass */
    if(!$userModel->validatePassword($_POST['password'])) {
        echo "Invalid password!";
    }
    
    /** @return bool | @param string $phone */
    if(!$userModel->validatePhone($_POST['telephone'])) {
        echo "Invalid telephone!";
    }
    
    /** @return bool | @param int $size */
    $user->recovery_token = $userModel->aleatoryToken(15);
    
    $user->save()
```

* **Guide to the above methods:**

- **hashFormat**: Encrypt a password using the native password_hash function.
- **urlFormat**: Formats a string to a friendly url.
```
"Pác^kà@gê Sí#mp%lePHP" -> "pac-ka-ge-si-mp-lephp"
```
- **validateEmail**: Validates that the email entered is valid. All true:
```
fulano@saopaulo.com.br
chico@gmail.com
joao@empresa.info.br
maria_silva@registro.br
rafa.sampaio@yahoo.com
fulano+de+tal@escola.ninja.br
```
- **validatePassword**: Checks whether the password contains an uppercase and lowercase letter, a special character, a number, if it is greater than 6 characters and less than 20.
```
123456 // false
QUASE123! // false
#OpA1 // false
#essaSenhaEGrande1234 // false
#OpA1? // true
Foi123! // true
```
- **validatePhone**: Checks whether the phone number you entered is valid. (hyphen not required)
```
(00)0000-0000
(00)00000-0000
0000-0000
00000-0000
```
- **aleatoryToken**: Generates a random string with the given size.

## Errors during Execution

When an error is generated by **SimplePHP**, it will appear in the directory **Source\Logs\Logs.txt** and will **always return null**.
* Examples logs:

```
-----SimplePHPLog-----
22/07/2020 12:53:26 -> Error: PDOCode 23000
-------
-----SimplePHPLog-----
22/07/2020 12:53:52 -> The primary index was not found.
-------
```

To see the error codes returned by the PDO, see [here](https://docstore.mik.ua/orelly/java-ent/jenut/ch08_06.htm).

## Authors

* **Nícollas Silva** - *Developer* - [NicollasSilva](https://github.com/nicollassilva)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
