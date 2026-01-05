<?php
session_start();
require_once('config.php');

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';

// Obter ID do Animal
$animalId = isset($_GET['id']) ? $_GET['id'] : null;
$animal = null;
$history = [];
$isNewSchedule = false;

// Obter Novo Agendamento (vindo do registro ou link direto)
if (isset($_GET['new_schedule']) && $_GET['new_schedule'] == 1) {
    $isNewSchedule = true;
}

require_once('classes/controllers/AnimalController.php');
require_once('classes/models/animal.php');
require_once('classes/models/especie.php');

// Inicializar Controlador
$controller = new AnimalController();

// Lógica de Controle de Acesso
$canEdit = false;
$isOwner = false;
$isAdminOrVet = ($isLoggedIn && ($userRole === 'admin' || $userRole === 'Veterinário'));

if (isset($animalId) && isset($controller)) {
    // Se o animal não foi carregado no topo (ex: após post), verificar se o temos
    if (!isset($animal)) {
         $animal = $controller->BuscarPeloId($animalId);
    }
    
    if ($animal) {
        $ownerId = $animal->IdUser;
        if ($isLoggedIn && $ownerId == $_SESSION['user_id']) {
            $isOwner = true;
        }
    }
}

// Fallback para nova página de agendamento (sem ID do animal ainda) -> Permitir edição (criação)
if ($isNewSchedule) {
    $canEdit = true;
} else if ($isOwner || $isAdminOrVet) {
    $canEdit = true;
}

// Pré-preencher campos se for novo agendamento a partir do POST
$animalName = isset($_POST['animal_name']) ? $_POST['animal_name'] : '';
$breed = isset($_POST['breed']) ? $_POST['breed'] : '';
$serviceDate = isset($_POST['service_date']) ? $_POST['service_date'] : date('Y-m-d'); // Usar data atual como padrão
$serviceType = isset($_POST['service_type']) ? $_POST['service_type'] : '';
$notes = isset($_POST['notes']) ? $_POST['notes'] : '';

// Carregar Dados do Animal Existente se não for novo agendamento
if (!$isNewSchedule && $animalId) {
    if (!$animal) {
         $animal = $controller->BuscarPeloId($animalId);
    }
    if ($animal) {
        $animalName = $animal->Nome; // Atualizar nome do animal para exibição
        $history = $controller->ListarProntuario($animalId);
    }
}

// Lidar com Criação de Novo Agendamento
$planNamePost = isset($_POST['plan_name']) ? $_POST['plan_name'] : null;

if ($isNewSchedule && !empty($animalName) && !empty($breed)) {
    // Includes fictícios para as dependências do Controlador se não carregados automaticamente
    // Assumindo que o AnimalController conecta ao BD sozinho como visto no arquivo
    
    $foto = isset($_FILES['animal_photo']) ? $_FILES['animal_photo'] : null;
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $newId = $controller->Cadastrar($animalName, $breed, $foto, $currentUserId, $planNamePost);
    if ($newId) {
        $animalId = $newId;
        $justCreated = true;
        $animal = $controller->BuscarPeloId($animalId); // Carregar para obter IdPlano
        // Opcionalmente auto-registrar o serviço inicial se desejado, ou deixar o usuário confirmar abaixo.
        // Fluxo atual: Usuário vê o formulário pré-preenchido e clica em Confirmar.
    }
}

// Carregar Tratamentos baseados no Plano
$planTreatments = [];
if (isset($animal) && !empty($animal->IdPlano)) {
    $planTreatments = $controller->GetTratamentosDoPlano($animal->IdPlano);
} else {
    // Fallback se nenhum plano ou nenhum tratamento encontrado: Permitir texto livre ou lista padrão
    // Ou poderíamos verificar se é um novo agendamento com nome do plano no POST (embora tenhamos acabado de salvar no BD/objeto Animal acima)
    if (!empty($planNamePost)) {
        $tempPlanId = $controller->GetPlanIdByName($planNamePost);
        $planTreatments = $controller->GetTratamentosDoPlano($tempPlanId);
    }
}

// Lidar com Ação de Salvar (Confirmar e Salvar)
if (isset($_POST['action']) && $_POST['action'] == 'save_treatment' && isset($_POST['animal_id'])) {
    $id = $_POST['animal_id'];
    $tratamento = $_POST['service_type'];
    $data = $_POST['service_date'];
    $obs = $_POST['notes'];
    
    // Validar se o usuário pode editar este animal
    $actionAnimal = $controller->BuscarPeloId($id);
    $canAction = false;
    if ($actionAnimal) {
         if ($isAdminOrVet || ($isLoggedIn && $actionAnimal->IdUser == $_SESSION['user_id'])) {
             $canAction = true;
         }
    } else if ($isNewSchedule) {
        // Permitir se acabou de ser criado (fluxo acima)
        $canAction = true;
    }
    
    if ($canAction) {
        if ($controller->RegistrarProntuario($id, $tratamento, $data, $obs)) {
            header("Location: atendimento.php?id=$id");
            exit;
        } else {
            echo "<script>alert('Erro ao salvar tratamento.');</script>";
        }
    } else {
        echo "<script>alert('Você não tem permissão para editar este prontuário.');</script>";
    }
}

// Lidar com Exclusão de Tratamento
if (isset($_GET['action']) && $_GET['action'] == 'delete_treatment' && isset($_GET['id']) && isset($_GET['date']) && isset($_GET['treatment'])) {
    $id = $_GET['id'];
    $treatment = $_GET['treatment'];
    $date = $_GET['date'];
    
    // Validar se o usuário pode excluir este tratamento
    $actionAnimal = $controller->BuscarPeloId($id);
    $canAction = false;
    if ($actionAnimal) {
         if ($isAdminOrVet || ($isLoggedIn && $actionAnimal->IdUser == $_SESSION['user_id'])) {
             $canAction = true;
         }
    }
    
    if ($canAction) {
        if ($controller->ExcluirTratamento($id, $treatment, $date)) {
            header("Location: atendimento.php?id=$id");
            exit;
        } else {
            echo "<script>alert('Erro ao excluir tratamento.');</script>";
        }
    } else {
        echo "<script>alert('Você não tem permissão para excluir este tratamento.');</script>";
    }
}

// Lidar com Ação de Atualizar (Atualizar Agendamento)
if (isset($_POST['action']) && $_POST['action'] == 'update_treatment' && isset($_POST['animal_id'])) {
    $id = $_POST['animal_id'];
    $tratamento = $_POST['service_type'];
    $data = $_POST['service_date'];
    $obs = $_POST['notes'];
    
    $oldData = $_POST['old_date'];
    $oldTratamento = $_POST['old_treatment'];

    // Validar se o usuário pode atualizar este tratamento
    $actionAnimal = $controller->BuscarPeloId($id);
    $canAction = false;
    if ($actionAnimal) {
         if ($isAdminOrVet || ($isLoggedIn && $actionAnimal->IdUser == $_SESSION['user_id'])) {
             $canAction = true;
         }
    }
    
    if ($canAction) {
        if ($controller->AtualizarTratamento($id, $oldData, $oldTratamento, $data, $tratamento, $obs)) {
            header("Location: atendimento.php?id=$id");
            exit;
        } else {
            echo "<script>alert('Erro ao atualizar tratamento.');</script>";
        }
    } else {
        echo "<script>alert('Você não tem permissão para atualizar este tratamento.');</script>";
    }
}

// Verificar Solicitação de Edição
$editMode = false;
$editData = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit_treatment' && isset($_GET['id']) && isset($_GET['date']) && isset($_GET['treatment'])) {
    $editMode = true;
    // Precisamos buscar os detalhes para este tratamento específico. 
    // Como ListarProntuario retorna todos, vamos apenas encontrá-lo no $history já buscado (se $animalId corresponder)
    // ou apenas confiamos nos parâmetros GET e no loop de histórico existente abaixo?
    // Melhor: Filtrar $history já que o carregamos para este ID.
    if ($animalId == $_GET['id']) {
        foreach ($history as $h) {
             if ($h->Tratamento == $_GET['treatment'] && $h->DataTratamento == $_GET['date']) {
                 $editData = $h;
                 // Pré-preencher variáveis do formulário
                 $serviceDate = date('Y-m-d', strtotime($h->DataTratamento));
                 $serviceType = $h->Tratamento;
                 $notes = $h->Descricao;
                 break;
             }
        }
    }
}

// Lidar com Ação de Excluir para Animal
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($isLoggedIn && ($userRole === 'admin' || $userRole === 'Veterinário')) {
        $idToDelete = $_GET['id'];
        if ($controller->Excluir($idToDelete)) {
            echo "<script>alert('Animal excluído com sucesso!'); window.location.href='index.php';</script>";
            exit;
        } else {
            echo "<script>alert('Erro ao excluir animal.');</script>";
        }
    } else {
        echo "<script>alert('Acesso negado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimento - Vida Pet</title>
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

        /* Estilo da Tabela */
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
            <img src="images/vetlife_logo.png" alt="Vida Pet Logo">
            Vida Pet
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
        
        <!-- Seção do Cabeçalho -->
        <div class="section-header">
            <!-- Preencher dados do novo animal se acabado de criar -->
            <?php if(isset($animal)): ?>
                <h1><?php echo htmlspecialchars($animal->Nome); ?></h1>
                <p><?php echo htmlspecialchars($animal->Especie->Nome); ?></p>
            <?php elseif($isNewSchedule): ?>
                <h1><?php echo htmlspecialchars($animalName); ?></h1>
                <p><?php echo htmlspecialchars($breed); ?></p>
            <?php endif; ?>
            <div>
                <?php if ($canEdit && isset($animalId)): ?>
                    <a href="atendimento.php?action=delete&id=<?php echo $animalId; ?>" class="btn-login" style="background-color: #dc3545; margin-right: 10px;" onclick="return confirm('Tem certeza que deseja excluir este animal e todo seu histórico?');">Excluir Animal</a>
                <?php endif; ?>
                <a href="index.php" class="btn-login" style="background-color: var(--text-light);">Voltar</a>
            </div>
        </div>

        <!-- Formulário de Tratamento -->
        <div class="section-card">
            <?php if ($isNewSchedule): ?>
                <div style="background-color: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #bee5eb;">
                    <strong>Agendamento Recebido!</strong> Abaixo está o resumo do pré-atendimento para confirmação.
                </div>
            <?php endif; ?>

            <?php if ($canEdit): ?>
            <form method="POST" action="atendimento.php">
                <input type="hidden" name="action" value="<?php echo $editMode ? 'update_treatment' : 'save_treatment'; ?>">
                <input type="hidden" name="animal_id" value="<?php echo isset($animalId) ? $animalId : ''; ?>">
                <?php if ($editMode): ?>
                    <input type="hidden" name="old_date" value="<?php echo htmlspecialchars($editData->DataTratamento); ?>">
                    <input type="hidden" name="old_treatment" value="<?php echo htmlspecialchars($editData->Tratamento); ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome do animal:</label>
                        <input type="text" value="<?php echo htmlspecialchars($animalName); ?>" disabled style="background-color: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Data:</label>
                        <input type="date" name="service_date" value="<?php echo htmlspecialchars($serviceDate); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tratamento Solicitado:</label>
                    <?php if (!empty($planTreatments)): ?>
                        <select name="service_type" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd; outline: none;">
                            <?php foreach ($planTreatments as $pt): ?>
                                <option value="<?php echo htmlspecialchars($pt); ?>" <?php echo ($serviceType == $pt) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pt); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" name="service_type" value="<?php echo htmlspecialchars($serviceType); ?>" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd;">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Observações do Cliente:</label>
                    <textarea name="notes" rows="5" style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd;"><?php echo htmlspecialchars($notes); ?></textarea>
                </div>

                <button type="submit" class="btn-submit" style="width: auto; padding: 0.8rem 2rem; background-color: <?php echo $editMode ? '#ffc107' : 'var(--primary-color)'; ?>; color: <?php echo $editMode ? '#000' : '#fff'; ?>;">
                    <?php echo $editMode ? 'Atualizar Agendamento' : 'Confirmar e Salvar'; ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="atendimento.php?id=<?php echo $animalId; ?>" class="btn-submit" style="background-color: #6c757d; text-decoration: none; display: inline-block; width: auto; padding: 0.8rem 2rem;">Cancelar Edição</a>
                <?php endif; ?>
            </form>
            <?php else: ?>
                <!-- Visualização Somente Leitura -->
                <div style="text-align: center;">
                    <img src="images/<?php echo isset($animalName) ? htmlspecialchars($animalName) : ''; ?>.png" alt="Foto do Animal" style="max-width: 200px; border-radius: 50%; border: 4px solid var(--primary-color); margin-bottom: 1rem;">
                    <h2 style="color: var(--primary-color);"><?php echo htmlspecialchars($animalName); ?></h2>
                    <p style="color: #666;">Você não tem permissão para editar este animal.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Seção de Histórico -->
        <?php if ($canEdit): ?>
        <div class="section-card">
            <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Histórico do Paciente</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="border-top-left-radius: 8px;">Data</th>
                        <th>Tratamento</th>
                        <th>Descrição do Tratamento</th>
                        <th style="border-top-right-radius: 8px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($history) > 0): ?>
                        <?php foreach ($history as $item): ?>
                            <tr>
                                <td><span class="tag-date"><?php echo date('d/m/Y', strtotime($item->DataTratamento)); ?></span></td>
                                <td><?php echo htmlspecialchars($item->Tratamento); ?></td>
                                <td><?php echo htmlspecialchars($item->Descricao ?: '-'); ?></td>
                                <td>
                                    <!-- Link de Edição -->
                                    <a href="atendimento.php?id=<?php echo $animalId; ?>&action=edit_treatment&date=<?php echo urlencode($item->DataTratamento); ?>&treatment=<?php echo urlencode($item->Tratamento); ?>" style="color: #ffc107; margin-right: 10px; text-decoration: none; font-weight: bold;">
                                        ✏️ Editar
                                    </a>
                                    <!-- Link de Cancelamento -->
                                    <a href="atendimento.php?id=<?php echo $animalId; ?>&action=delete_treatment&date=<?php echo urlencode($item->DataTratamento); ?>&treatment=<?php echo urlencode($item->Tratamento); ?>" style="color: #dc3545; text-decoration: none; font-weight: bold;" onclick="return confirm('Deseja cancelar este agendamento?');">
                                        🗑️ Cancelar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">Nenhum histórico encontrado para este paciente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5511999999999" class="whatsapp-float" target="_blank">
        <img src="images/whatsapp_icon.png" alt="WhatsApp">
    </a>

</body>
</html>