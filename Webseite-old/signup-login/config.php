<?php

namespace Database;

class Connection {
    private $connection;

    public function __construct($db_name, $username, $password) {
        $this->connection = new \PDO($db_name, $username, $password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection() {
        return $this->connection;
    }
}

// Autoloader
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

use Database\Connection;

$db_name = "mysql:host=localhost;dbname=user_form";
$username = "root";
$password = "";

$databaseConnection = new Connection($db_name, $username, $password);
$conn = $databaseConnection->getConnection();

// Hier kannst du $conn verwenden, um auf die Datenbank zuzugreifen

?>
