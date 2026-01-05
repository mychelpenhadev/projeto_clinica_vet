<?php
require_once('config.php');

$servidor = 'mysql:host=localhost;dbname=prontuario_vet';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO($servidor, $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Planes Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS planos (
        cd_plano INT AUTO_INCREMENT PRIMARY KEY,
        nm_plano VARCHAR(50) NOT NULL,
        valor DECIMAL(10,2)
    )");

    // 2. Create Planes-Treatments Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS plano_tratamento (
        cd_plano INT,
        cd_tratamento INT,
        PRIMARY KEY (cd_plano, cd_tratamento),
        FOREIGN KEY (cd_plano) REFERENCES planos(cd_plano),
        FOREIGN KEY (cd_tratamento) REFERENCES tratamento(cd_tratamento)
    )");

    // 3. Add id_plano to animal table
    try {
        $pdo->exec("ALTER TABLE animal ADD COLUMN id_plano INT");
        $pdo->exec("ALTER TABLE animal ADD CONSTRAINT fk_animal_plano FOREIGN KEY (id_plano) REFERENCES planos(cd_plano)");
        echo "Column id_plano added to animal table.\n";
    } catch (PDOException $e) {
        // Column might already exist
        echo "Column id_plano might already exist: " . $e->getMessage() . "\n";
    }

    // 4. Populate Plans
    $plans = [
        ['Amigo Fiel', 89.90],
        ['Proteção Total', 149.90],
        ['VIP Pet', 299.90]
    ];

    foreach ($plans as $p) {
        $stmt = $pdo->prepare("SELECT cd_plano FROM planos WHERE nm_plano = ?");
        $stmt->execute([$p[0]]);
        if (!$stmt->fetch()) {
            $stmtIns = $pdo->prepare("INSERT INTO planos (nm_plano, valor) VALUES (?, ?)");
            $stmtIns->execute($p);
            echo "Inserted plan: " . $p[0] . "\n";
        }
    }

    // 5. Populate Treatment-Plan Mapping (Simplistic Mapping based on names)
    // First, let's ensure treatments exist (from previous seeding or default)
    // We will manually map known treatments to IDs based on the provided list
    
    // Amigo Fiel: Consultas, Vacinas Nacionais
    // Proteção Total: + Raio-X, Castração, Limpeza, Vacinas Importadas
    // VIP Pet: + Fisioterapia, Acupuntura, Spa, Hospedagem
    
    // Helper to get IDs
    function getPlanId($pdo, $name) {
        $stmt = $pdo->prepare("SELECT cd_plano FROM planos WHERE nm_plano = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }
    
    function getTratId($pdo, $name) {
        $stmt = $pdo->prepare("SELECT cd_tratamento FROM tratamento WHERE nm_tratamento = ?");
        $stmt->execute([$name]);
        $id = $stmt->fetchColumn();
        if (!$id) {
             // Create if not exists ? No, let's assume they are created dynamically or we create them now
             $stmtMax = $pdo->query("SELECT MAX(cd_tratamento) FROM tratamento");
             $max = $stmtMax->fetchColumn();
             $id = ($max ? $max : 0) + 1;
             $stmtIns = $pdo->prepare("INSERT INTO tratamento (cd_tratamento, nm_tratamento, ds_tratamento) VALUES (?, ?, 'Auto-created')");
             $stmtIns->execute([$id, $name]);
        }
        return $id;
    }

    function linkPlan($pdo, $planName, $treatName) {
        $pid = getPlanId($pdo, $planName);
        $tid = getTratId($pdo, $treatName);
        if ($pid && $tid) {
            try {
                $pdo->exec("INSERT INTO plano_tratamento (cd_plano, cd_tratamento) VALUES ($pid, $tid)");
            } catch (PDOException $e) { /* Ignore duplicate */ }
        }
    }

    // Map treatments
    $common = ['Consulta', 'Vacina Antirrábica', 'Vermifugação'];
    $advanced = ['Exame de Sangue', 'Raio-X', 'Castração', 'Limpeza de Tártaro'];
    $vip = ['Fisioterapia', 'Acupuntura', 'Banho e Tosa', 'Hospedagem'];

    foreach ($common as $t) {
        linkPlan($pdo, 'Amigo Fiel', $t);
        linkPlan($pdo, 'Proteção Total', $t);
        linkPlan($pdo, 'VIP Pet', $t);
    }
    foreach ($advanced as $t) {
        linkPlan($pdo, 'Proteção Total', $t);
        linkPlan($pdo, 'VIP Pet', $t);
    }
    foreach ($vip as $t) {
        linkPlan($pdo, 'VIP Pet', $t);
    }

    echo "Database migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
