<?php
session_start();
require_once('config.php');
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - Vida Pet</title>
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
            <li><a href="plans.php" style="color: var(--primary-color); font-weight: bold;">Planos</a></li>
            <li><a href="index.php#sobre">Sobre a Empresa</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li>Olá, <?php echo htmlspecialchars($userName); ?> (<?php echo ucfirst($userRole); ?>)</li>
                <li><a href="logout.php" class="btn-login" style="background-color: #666;">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="pricing-header">
        <h1>Planos de Saúde Vida Pet</h1>
        <p>Cuide de quem sempre está ao seu lado. Escolha a melhor proteção para o seu pet.</p>
    </div>

    <div class="pricing-container">
        <!-- Plano Básico -->
        <div class="pricing-card">
            <h3>Amigo Fiel</h3>
            <p>Prevenção básica e econômica.</p>
            <div class="price"><small>R$</small> 89,90<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Consultas Agendadas (Seg-Sex)</li>
                <li>Aplicação de Vacinas Nacionais</li>
                <li>Microchipagem Gratuita</li>
                <li>Suporte por WhatsApp</li>
            </ul>
            <a href="checkout.php?plan=Amigo Fiel&price=89,90" class="btn-plan">Escolher este</a>
        </div>

        <!-- Plano Avançado -->
        <div class="pricing-card featured">
            <h3>Proteção Total</h3>
            <p>Segurança completa para a saúde.</p>
            <div class="price"><small>R$</small> 149,90<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Pronto Socorro 24 Horas</li>
                <li>Exames de Sangue e Raio-X</li>
                <li>Castração (Cães e Gatos)</li>
                <li>Limpeza de Tártaro</li>
                <li>Vacinas Importadas V10 e V8</li>
            </ul>
            <a href="checkout.php?plan=Proteção Total&price=149,90" class="btn-plan">Escolher este</a>
        </div>

        <!-- Plano Clínica -->
        <div class="pricing-card">
            <h3>VIP Pet</h3>
            <p>O máximo de luxo e conveniência.</p>
            <div class="price"><small>R$</small> 299,90<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Médico Veterinário em Domicílio</li>
                <li>Cirurgias Complexas e Ortopedia</li>
                <li>Fisioterapia e Acupuntura</li>
                <li>Spa Day (Banho, Tosa e Hidratação)</li>
                <li>Hospedagem Premium (Semana Inteira)</li>
                <li>Transporte Leva e Traz</li>
            </ul>
            <a href="checkout.php?plan=VIP Pet&price=299,90" class="btn-plan">Escolher este</a>
        </div>
    </div>

    <section class="modules-section">
        <h2 class="section-title">Serviços Adicionais</h2>
        <div class="modules-grid">
            <div class="module-card">
                <h4>Microchipagem</h4>
                <p>Identificação eletrônica permanente para seu pet, garantindo mais segurança em caso de perda.</p>
                <p style="margin-top: 10px; font-weight: bold;">R$ 150,00 (único)</p>
            </div>
            <div class="module-card">
                <h4>Odontologia Preventiva</h4>
                <p>Limpeza de tártaro e polimento para manter a saúde bucal do seu amigo em dia.</p>
                <p style="margin-top: 10px; font-weight: bold;">R$ 250,00/sessão</p>
            </div>
            <div class="module-card">
                <h4>Taxi Dog</h4>
                <p>Buscamos e levamos seu pet para banho, tosa ou consultas com total segurança e conforto.</p>
                <p style="margin-top: 10px; font-weight: bold;">Consulte valores</p>
            </div>
        </div>
    </section>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <img src="images/whatsapp_icon.png" alt="WhatsApp">
    </a>

    <footer>
        <p>&copy; 2026 Vida Pet Clínica Veterinária. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
