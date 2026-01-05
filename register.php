<?php
session_start();
require_once('config.php');

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "As senhas não coincidem!";
    } else {
        $userController = new UserController();
        $result = $userController->Register($name, $email, $password);

        if ($result === true) {
            $success = "Conta criada com sucesso! Você pode fazer login agora.";
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Vida Pet</title>
    <link rel="stylesheet" href="css/modern.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/vetlife_logo.png" alt="Vida Pet Logo">
            Vida Pet
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
        </ul>
    </nav>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Crie sua conta</h2>
            
            <?php if($error): ?>
                <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <?php if($success): ?>
                <p style="color: green; margin-bottom: 1rem;"><?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-submit">Cadastrar</button>
            </form>

            <div class="social-login">
                <p style="margin-bottom: 1rem; color: #666;">Ou cadastre-se com</p>
                <button class="btn-social btn-google">
                    Google
                </button>
                <button class="btn-social btn-facebook">
                    Facebook
                </button>
            </div>

            <p style="margin-top: 1.5rem;">
                Já tem uma conta? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Faça Login</a>
            </p>
        </div>
    </div>
</body>
</html>
