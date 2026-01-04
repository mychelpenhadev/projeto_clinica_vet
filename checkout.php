<?php
session_start();
require_once('config.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';

$planName = isset($_GET['plan']) ? $_GET['plan'] : 'Plano Desconhecido';
$planPrice = isset($_GET['price']) ? $_GET['price'] : '0,00';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VetLife</title>
    <link rel="stylesheet" href="css/modern.css">
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("payment-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].className = tabcontent[i].className.replace(" active", "");
            }
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).className += " active";
            evt.currentTarget.className += " active";
        }
    </script>
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
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="checkout-container">
        <!-- Order Summary -->
        <div class="order-summary">
            <h3>Resumo do Pedido</h3>
            <p style="font-size: 1.1rem; color: #666;">Plano Selecionado:</p>
            <p style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-bottom: 1rem;">
                <?php echo htmlspecialchars($planName); ?>
            </p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 1rem 0;">
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                <span>Total:</span>
                <span>R$ <?php echo htmlspecialchars($planPrice); ?>/mês</span>
            </div>
        </div>

        <!-- Payment Area -->
        <div class="payment-box">
            <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Escolha a forma de pagamento</h2>
            
            <div class="payment-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'Pix')">Pix</button>
                <button class="tab-btn" onclick="openTab(event, 'Cartao')">Cartão de Crédito</button>
                <button class="tab-btn" onclick="openTab(event, 'Boleto')">Boleto</button>
            </div>

            <!-- Pix Content -->
            <div id="Pix" class="payment-content active">
                <div class="pix-container">
                    <p style="margin-bottom: 1rem; color: var(--secondary-color); font-weight: bold;">Aprovação Imediata!</p>
                    <div class="qr-placeholder">
                        <img src="images/vetlife_logo.png" style="width: 50px; opacity: 0.5;">
                        <br>[QR CODE]
                    </div>
                    <p>Escaneie o QR Code ou use o código abaixo:</p>
                    <div class="copy-paste-code">
                        00020126360014BR.GOV.BCB.PIX0114+5511999999995204000053039865802BR5913VETLIFE CLINICA6009SAO PAULO62070503***63041D3D
                    </div>
                    <a href="animal_registration.php?plan=<?php echo urlencode($planName); ?>" class="btn-plan" style="display:block; text-align:center; text-decoration:none;">Copiar Código Pix e Avançar</a>
                </div>
            </div>

            <!-- Credit Card Content -->
            <div id="Cartao" class="payment-content">
                <form>
                    <div class="form-group">
                        <label>Número do Cartão</label>
                        <input type="text" placeholder="0000 0000 0000 0000">
                    </div>
                    <div class="form-group">
                        <label>Nome no Cartão</label>
                        <input type="text" placeholder="Nome como impresso">
                    </div>
                    <div class="card-grid">
                        <div class="form-group">
                            <label>Validade</label>
                            <input type="text" placeholder="MM/AA">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" placeholder="123">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Parcelamento</label>
                        <select style="width: 100%; padding: 0.8rem; border-radius: var(--radius); border: 1px solid #ddd;">
                            <option>1x de R$ <?php echo htmlspecialchars($planPrice); ?> sem juros</option>
                            <option>2x sem juros</option>
                            <option>3x sem juros</option>
                        </select>
                    </div>
                    <a href="animal_registration.php?plan=<?php echo urlencode($planName); ?>" class="btn-plan" style="display:block; text-align:center; text-decoration:none;">Finalizar Pagamento</a>
                </form>
            </div>

            <!-- Boleto Content -->
            <div id="Boleto" class="payment-content">
                <div class="boleto-info">
                    <p>O boleto será gerado após clicar no botão abaixo.</p>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 1rem;">O prazo de compensação do boleto pode levar até 3 dias úteis.</p>
                    <a href="animal_registration.php?plan=<?php echo urlencode($planName); ?>" class="btn-plan" style="display:block; text-align:center; text-decoration:none; background-color: #666;">Gerar Boleto</a>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
