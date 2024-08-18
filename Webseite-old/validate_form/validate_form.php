<?php

namespace App; // Namespace-Deklaration (Namespaces)

// Autoloader
spl_autoload_register(function ($class) {
    // Konvertiert den Namespace in einen relativen Dateipfad
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    // Überprüft, ob die Datei existiert, und lädt sie
    if (file_exists($file)) {
        require_once $file;
    }
});

class UserValidator {
    private array $data; // Type Declaration (Type Hints)
    private array $errors = [];
    private static array $fields = ['first-name', 'last-name', 'mail', 'plz', 'address', 'phone', 'subjekt', 'message'];

    public function __construct(array $post_data) {
        $this->data = $post_data;
    }

    // Methoden-Sichtbarkeit (public)
    public function validateForm(): array {
        foreach (self::$fields as $field) {
            if (empty($this->data[$field])) {
                $this->addError($field, ucfirst(str_replace('-', ' ', $field)) . " is required");
            }
        }

        return $this->errors;
    }

    private function addError(string $key, string $error): void {
        $this->errors[$key] = $error;
    }
}

// Serverseitige Validierung
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validator = new \App\UserValidator($_POST); // Namespace Usage
    $errors = $validator->validateForm();

    if (!empty($errors)) {
        echo "<div style='color: red;'>Folgende Fehler sind aufgetreten:<br>";
        foreach ($errors as $error) {
            echo "- $error<br>";
        }
        echo "</div>";
    } else {
        echo "<div style='color: green;'>Nachricht erfolgreich gesendet!</div>";
        // Hier könnte die Weiterleitung zur nächsten Seite erfolgen
    }
}
?>
