<?php
session_start();
require_once('config.php');

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$plan = isset($_GET['plan']) ? $_GET['plan'] : 'Padrão';

// Logic to define services based on Plan
$services = [];
if (strpos($plan, 'Básico') !== false) {
    $services = ['Banho', 'Tosa Higiênica', 'Corte de Unhas'];
} elseif (strpos($plan, 'Avançado') !== false) {
    $services = ['Banho', 'Tosa Completa', 'Hidratação', 'Corte de Unhas', 'Limpeza de Ouvidos'];
} elseif (strpos($plan, 'Clínica') !== false) {
    $services = ['Consulta Geral', 'Vacinação', 'Exames Laboratoriais', 'Ultrassonografia', 'Cirurgia (Avaliação)', 'Internação'];
} else {
    // Default fallback
    $services = ['Banho', 'Tosa', 'Consulta'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - VetLife</title>
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .schedule-container {
            max-width: 600px;
            margin: 3rem auto;
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
        }
        .success-banner {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/vetlife_logo.png" alt="VetLife Logo">
            VetLife
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
            <li><a href="plans.php">Planos</a></li>
            <?php if ($isLoggedIn): ?>
                <li>Olá, <?php echo htmlspecialchars($userName); ?></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="schedule-container">
        <div class="success-banner">
            <h3>Pagamento Confirmado! 🎉</h3>
            <p>Agora vamos agendar o serviço para o seu pet (<?php echo htmlspecialchars($plan); ?>).</p>
        </div>

        <form action="atendimento.php?new_schedule=1" method="POST">
            <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Dados do Animal & Serviço</h2>
            
            <div class="form-group">
                <label>Nome do Animal</label>
                <input type="text" name="animal_name" required placeholder="Ex: Rex">
            </div>

            <div class="form-group">
                <label>Descrição da Raça / Espécie</label>
                <input type="text" name="breed" required placeholder="Ex: Golden Retriever, Gato Persa...">
            </div>

            <div class="form-group">
                <label>Data para o Serviço</label>
                <input type="date" name="service_date" required>
            </div>

            <div class="form-group">
                <label>Selecione o Serviço (Incluso no <?php echo htmlspecialchars($plan); ?>)</label>
                <select name="service_type" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd; outline: none;">
                    <?php foreach ($services as $svc): ?>
                        <option value="<?php echo htmlspecialchars($svc); ?>"><?php echo htmlspecialchars($svc); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Descrição Adicional (O que mais você deseja?)</label>
                <textarea name="notes" rows="4" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd; outline: none;" placeholder="Ex: O animal tem alergia a perfume, cuidado com a pata esquerda..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Confirmar Agendamento</button>
        </form>
    </div>
</body>
</html>
