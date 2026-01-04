<?php
session_start();
require_once('config.php');

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';

// Capture data from Registration if available
$animalName = isset($_POST['animal_name']) ? $_POST['animal_name'] : 'Brutos (Exemplo)';
$serviceDate = isset($_POST['service_date']) ? $_POST['service_date'] : date('Y-m-d');
$serviceType = isset($_POST['service_type']) ? $_POST['service_type'] : '';
$notes = isset($_POST['notes']) ? $_POST['notes'] : '';
$breed = isset($_POST['breed']) ? $_POST['breed'] : ''; // Not used in form currently but good to have

$isNewSchedule = isset($_GET['new_schedule']);

if ($isNewSchedule && !empty($animalName) && !empty($breed)) {
    require_once('classes/controllers/AnimalController.php');
    // Dummy includes for the Controller dependencies if not auto-loaded
    // Assuming AnimalController connects to DB itself as seen in file
    
    $controller = new AnimalController();
    $controller->Cadastrar($animalName, $breed);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimento - VetLife</title>
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .atendimento-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .section-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 1rem;
        }

        .section-header h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Table Styling */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .history-table th, .history-table td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .history-table th {
            color: var(--primary-color);
            font-weight: bold;
            background-color: var(--accent-color);
        }

        .history-table tr:hover {
            background-color: #f9f9f9;
        }

        .tag-date {
            background: var(--primary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
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
                <li><a href="logout.php" class="btn-login" style="background-color: #666;">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="atendimento-container">
        
        <!-- Header Section -->
        <div class="section-header">
            <h1>Registro de Atendimento</h1>
            <a href="index.php" class="btn-login" style="background-color: var(--text-light);">Voltar</a>
        </div>

        <!-- Treatment Form -->
        <div class="section-card">
            <?php if ($isNewSchedule): ?>
                <div style="background-color: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #bee5eb;">
                    <strong>Agendamento Recebido!</strong> Abaixo está o resumo do pré-atendimento para confirmação.
                </div>
            <?php endif; ?>

            <form>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome do animal:</label>
                        <input type="text" value="<?php echo htmlspecialchars($animalName); ?>" disabled style="background-color: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Data:</label>
                        <input type="date" value="<?php echo htmlspecialchars($serviceDate); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tratamento Solicitado:</label>
                    <input type="text" value="<?php echo htmlspecialchars($serviceType); ?>" disabled style="background-color: #f5f5f5; width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd;">
                </div>

                <div class="form-group">
                    <label>Observações do Cliente:</label>
                    <textarea rows="5" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd;"><?php echo htmlspecialchars($notes); ?></textarea>
                </div>

                <button class="btn-submit" style="width: auto; padding: 0.8rem 2rem;">Confirmar e Salvar</button>
            </form>
        </div>

        <!-- History Section -->
        <div class="section-card">
            <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Histórico do Paciente</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="border-top-left-radius: 8px;">Data</th>
                        <th>Tratamento</th>
                        <th style="border-top-right-radius: 8px;">Descrição do Tratamento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="tag-date">30/08/2024</span></td>
                        <td>Vermifugação</td>
                        <td>Houve reação alérgica e foi administrado Apoquel 6g</td>
                    </tr>
                    <tr>
                        <td><span class="tag-date">30/08/2024</span></td>
                        <td>Vacina Antirrábica</td>
                        <td>Renovar em 1 ano</td>
                    </tr>
                    <tr>
                        <td><span class="tag-date">15/07/2024</span></td>
                        <td>Consulta Geral</td>
                        <td>Animal apresentava falta de apetite.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <img src="images/whatsapp_icon.png" alt="WhatsApp">
    </a>

</body>
</html>