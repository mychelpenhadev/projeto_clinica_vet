<?php
session_start();
require_once('config.php');
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - VetLife</title>
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
        <h1>Um sistema completo para sua empresa pet</h1>
        <p>Escolha o plano que atende melhor a necessidade do seu negócio.</p>
    </div>

    <div class="pricing-container">
        <!-- Plano Básico -->
        <div class="pricing-card">
            <h3>Pet Shop Básico</h3>
            <p>Pensado para operações essenciais.</p>
            <div class="price">R$ 157<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Controle de pacotes e kits</li>
                <li>Agenda com recorrência</li>
                <li>Ponto de venda (PDV)</li>
                <li>Contas a pagar</li>
                <li>Entrada e saída de estoque</li>
                <li>Até 80 notas/mês (NFC-e/NFS-e)</li>
                <li>Até 3 usuários</li>
            </ul>
            <a href="checkout.php?plan=Pet Shop Básico&price=157" class="btn-plan">Experimentar</a>
        </div>

        <!-- Plano Avançado -->
        <div class="pricing-card featured">
            <h3>Pet Shop Avançado</h3>
            <p>Para controle estratégico.</p>
            <div class="price">R$ 220<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Tudo do Básico +</li>
                <li>Emissão ilimitada de notas</li>
                <li>Controle de comissões</li>
                <li>Demonstrativo financeiro</li>
                <li>Análise de consumo</li>
                <li>Sugestão de compra</li>
                <li>Fluxo de caixa</li>
                <li>Até 5 usuários</li>
            </ul>
            <a href="checkout.php?plan=Pet Shop Avançado&price=220" class="btn-plan">Experimentar</a>
        </div>

        <!-- Plano Clínica -->
        <div class="pricing-card">
            <h3>Clínica e Hospital</h3>
            <p>Para clínicas e hospitais.</p>
            <div class="price">R$ 359<span>/mês</span></div>
            <ul class="pricing-features">
                <li>Prontuário veterinário</li>
                <li>Controle de vacina</li>
                <li>Análise de estoque</li>
                <li>Controle de internação (add)</li>
                <li>Vendas integradas</li>
                <li>Fiscal ilimitado</li>
                <li>A partir de 3 usuários</li>
            </ul>
            <a href="checkout.php?plan=Clínica e Hospital&price=359" class="btn-plan">Experimentar</a>
        </div>
    </div>

    <section class="modules-section">
        <h2 class="section-title">Módulos Adicionais</h2>
        <div class="modules-grid">
            <div class="module-card">
                <h4>Módulo Fiscal (Clínica)</h4>
                <p>Emita suas notas fiscais grandes (NF-e), notas de produtos (NFC-e) e notas de serviços (NFS-e) num só lugar.</p>
                <p style="margin-top: 10px; font-weight: bold;">R$ 153,00/mês</p>
            </div>
            <div class="module-card">
                <h4>Módulo Internação</h4>
                <p>Acompanhe os animais internados de qualquer lugar e confira horários de prescrições.</p>
                <p style="margin-top: 10px; font-weight: bold;">R$ 136,00/mês</p>
            </div>
            <div class="module-card">
                <h4>Mensagens Automáticas</h4>
                <p>Avisos por WhatsApp, SMS e E-mail sobre agendamentos e aniversários.</p>
                <p style="margin-top: 10px; font-weight: bold;">R$ 0,50/WhatsApp</p>
            </div>
        </div>
    </section>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <img src="images/whatsapp_icon.png" alt="WhatsApp">
    </a>

    <footer>
        <p>&copy; 2026 VetLife Clínica Veterinária. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
