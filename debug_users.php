<?php
require_once('config.php');

$servidor = 'mysql:host=localhost;dbname=prontuario_vet';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO($servidor, $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id_user, name, email, role, password FROM users");
    echo "Users found:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id_user'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . " | Role: " . $row['role'] . "\n";
        // Verify if default admin password matches
        if ($row['email'] == 'admin@vetlife.com') {
             $isMatch = password_verify('123456', $row['password']) ? 'YES' : 'NO';
             echo "Matches '123456'? " . $isMatch . "\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
