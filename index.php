<?php
    session_start();
    require_once('config.php');

    $buscar = false;
    $valor = "";

    if (isset($_GET['valorBusca']))
    {
        $buscar = true;
        if ($_GET['valorBusca'] != "")
        {
            $valor = $_GET['valorBusca'];
        }
    }
    
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
    <title>VetLife - Clínica Veterinária</title>
    <!-- Modern CSS -->
    <link rel="stylesheet" href="css/modern.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <img src="images/vetlife_logo.png" alt="VetLife Logo">
            VetLife
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
            <li><a href="plans.php">Planos</a></li>
            <li><a href="#vets">Veterinários</a></li>
            <li><a href="#sobre">Sobre a Empresa</a></li>
            <li><a href="#suporte">Suporte</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li>Olá, <?php echo htmlspecialchars($userName); ?> (<?php echo ucfirst($userRole); ?>)</li>
                <li><a href="logout.php" class="btn-login" style="background-color: #666;">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Cuidando com Amor do seu Melhor Amigo</h1>
        <p>A VetLife oferece os melhores cuidados veterinários com profissionais altamente qualificados e tecnologia de ponta para o bem-estar do seu pet.</p>
        
        <form id="area-busca" action="index.php" method="get" class="search-container">
            <input type="text" name="valorBusca" placeholder="Pesquisar animal por nome...">
            <button>Buscar</button>
        </form>
    </section>

    <!-- Veterinarians Section -->
    <h2 class="section-title" id="vets">Nossos Veterinários</h2>
    <section class="vets-container">
        <div class="vet-card">
            <img src="images/vet_doctor_1.png" alt="Dra. Ana Silva">
            <div class="vet-info">
                <h3>Dra. Ana Silva</h3>
                <p>Especialista em Dermatologia</p>
                <p>CRMV 12345</p>
            </div>
        </div>
        <div class="vet-card">
            <img src="images/vet_doctor_2.png" alt="Dr. Marcos Santos">
            <div class="vet-info">
                <h3>Dr. Marcos Santos</h3>
                <p>Cirurgião Geral</p>
                <p>CRMV 67890</p>
            </div>
        </div>
        <div class="vet-card">
            <img src="images/vet_doctor_1.png" alt="Dra. Julia Oliveira">
            <div class="vet-info">
                <h3>Dra. Julia Oliveira</h3>
                <p>Cardiologia Veterinária</p>
                <p>CRMV 54321</p>
            </div>
        </div>
    </section>

    <!-- Animals Results Section -->
    <?php if($buscar): ?>
        <h2 class="section-title">Resultados da Busca</h2>
        <section id="resultados">
            <?php
                if($buscar)
                {
                    $animalView = new AnimalView();
                    if ($valor == "")
                    {
                        $animalView->ExibirTodosAnimais();
                    }
                    else
                    {
                        $animalView->BuscarPeloNome($valor);
                    }
                }
            ?>       
        </section>
    <?php else: ?>
        <h2 class="section-title">Nossos Pacientes</h2>
        <section id="resultados">
            <?php
                $animalView = new AnimalView();
                $animalView->ExibirTodosAnimais();
            ?>
        </section>
    <?php endif; ?>

    <!-- Extra Sections placeholder -->
    <section id="sobre" style="padding: 4rem 5%; background-color: white; text-align: center;">
        <h2 style="color: var(--primary-color); margin-bottom: 2rem;">Sobre a Empresa</h2>
        <p style="max-width: 800px; margin: 0 auto; color: #666;">
            A VetLife nasceu da paixão pelos animais. Fundada em 2010, nossa missão é proporcionar saúde e qualidade de vida para cães e gatos, com um atendimento humanizado e transparente. Contamos com uma estrutura completa de exames, cirurgia e internação 24h.
        </p>
    </section>

    <section id="suporte" style="padding: 4rem 5%; background-color: var(--accent-color); text-align: center;">
        <h2 style="color: var(--primary-color); margin-bottom: 2rem;">Suporte</h2>
        <p style="margin-bottom: 1rem;">Precisa de agendamento ou tem alguma dúvida?</p>
        <p style="font-weight: bold; font-size: 1.2rem;">(11) 99999-9999 / contato@vetlife.com.br</p>
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
