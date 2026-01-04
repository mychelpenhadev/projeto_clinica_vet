<?php
session_start();
require_once('config.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userController = new UserController();
    if ($userController->Login($email, $password)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Email ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VetLife</title>
    <link rel="stylesheet" href="css/modern.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/vetlife_logo.png" alt="VetLife Logo">
            VetLife
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
        </ul>
    </nav>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Bem-vindo de volta!</h2>
            
            <?php if($error): ?>
                <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">Entrar</button>
            </form>

            <div class="social-login">
                <p style="margin-bottom: 1rem; color: #666;">Ou entre com suas redes sociais</p>
                <button class="btn-social btn-google">
                    Google
                </button>
                <button class="btn-social btn-facebook">
                    Facebook
                </button>
            </div>

            <p style="margin-top: 1.5rem;">
                Não tem uma conta? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Cadastre-se</a>
            </p>
        </div>
    </div>
</body>
</html>
