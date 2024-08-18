<?php

namespace Authentication;

class Logout {
    public function __construct() {
        session_start();
    }

    public function logoutUser() {
        session_unset();
        session_destroy();
        header('location:login.php');
        exit(); // Beende die Ausführung nach der Umleitung
    }
}

// Autoloader
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

use Authentication\Logout;

$logout = new Logout();
$logout->logoutUser();

?>
