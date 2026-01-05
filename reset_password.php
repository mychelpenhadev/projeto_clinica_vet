<?php
require_once('config.php');

$servidor = 'mysql:host=localhost;dbname=prontuario_vet';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO($servidor, $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $newHash = password_hash('123456', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = :pass WHERE email = 'admin@vetlife.com'");
    $stmt->bindParam(':pass', $newHash);
    $stmt->execute();
    
    echo "Admin password reset to '123456' successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
