<?php

class AnimalController {

    function Listar()
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        $lista = [];
        try
         {
            
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cSQL = $pdo->prepare('select cd_animal, nm_animal, cd_especie, id_user, id_plano from animal');
            $cSQL->execute();

            while ($dados = $cSQL->fetch(PDO::FETCH_ASSOC))
            {
                $codigo = $dados['cd_animal'];
                $nome = $dados['nm_animal'];
                $codigoEspecie = $dados['cd_especie'];
                $idUser = $dados['id_user'];
                $idPlano = isset($dados['id_plano']) ? $dados['id_plano'] : null;

                $cSQL_Especie = $pdo->prepare('Select nm_especie from especie where cd_especie = :codigo');
                $cSQL_Especie->bindParam('codigo', $codigoEspecie);
                $cSQL_Especie->execute();

                $dadosEspecie = $cSQL_Especie->fetch(PDO::FETCH_ASSOC);
                $nomeEspecie = $dadosEspecie['nm_especie'];

                $especie = new Especie($codigoEspecie, $nomeEspecie);
                
                $animal = new Animal($codigo, $nome, $especie, $idUser, $idPlano);
                array_push($lista, $animal);
            }
            $pdo = null;
        } 
        catch (PDOException $e) 
        {
            echo 'Erro:' . $e->getMessage();
        }
        return $lista;
    }

    function BuscarPeloNome($nome)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        $lista = [];
        try
         {
            
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cSQL = $pdo->prepare('
                SELECT a.cd_animal, a.nm_animal, a.cd_especie, a.id_user, a.id_plano 
                FROM animal a
                INNER JOIN especie e ON a.cd_especie = e.cd_especie
                WHERE a.nm_animal LIKE :nome OR e.nm_especie LIKE :nome
            ');
            $nomeBusca = "%" . $nome . "%";
            $cSQL->bindParam('nome', $nomeBusca);
            $cSQL->execute();

            while ($dados = $cSQL->fetch(PDO::FETCH_ASSOC))
            {
                $codigo = $dados['cd_animal'];
                $nome = $dados['nm_animal'];
                $codigoEspecie = $dados['cd_especie'];
                $idUser = $dados['id_user'];
                $idPlano = isset($dados['id_plano']) ? $dados['id_plano'] : null;

                $cSQL_Especie = $pdo->prepare('Select nm_especie from especie where cd_especie = :codigo');
                $cSQL_Especie->bindParam('codigo', $codigoEspecie);
                $cSQL_Especie->execute();

                $dadosEspecie = $cSQL_Especie->fetch(PDO::FETCH_ASSOC);
                $nomeEspecie = $dadosEspecie['nm_especie'];

                $especie = new Especie($codigoEspecie, $nomeEspecie);
                
                $animal = new Animal($codigo, $nome, $especie, $idUser, $idPlano);
                array_push($lista, $animal);
            }
            $pdo = null;
        } 
        catch (PDOException $e) 
        {
            echo 'Erro:' . $e->getMessage();
        }
        return $lista;
    }
    function Cadastrar($nomeAnimal, $nomeEspecie, $foto = null, $idUser = null, $planName = null)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. Verificar/Inserir Espécie
            $stmt = $pdo->prepare("SELECT cd_especie FROM especie WHERE nm_especie = :especie");
            $stmt->bindParam(':especie', $nomeEspecie);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $cd_especie = $row['cd_especie'];
            } else {
                // Obter próximo ID disponível para espécie
                $stmtId = $pdo->query("SELECT MAX(cd_especie) as max_id FROM especie");
                $rowId = $stmtId->fetch(PDO::FETCH_ASSOC);
                $cd_especie = ($rowId['max_id'] ? $rowId['max_id'] : 0) + 1;

                $stmtInsertEsp = $pdo->prepare("INSERT INTO especie (cd_especie, nm_especie) VALUES (:id, :nome)");
                $stmtInsertEsp->bindParam(':id', $cd_especie);
                $stmtInsertEsp->bindParam(':nome', $nomeEspecie);
                $stmtInsertEsp->execute();
            }

            // Obter ID do Plano
            $idPlano = null;
            if ($planName) {
                $idPlano = $this->GetPlanIdByName($planName);
            }

            // 2. Inserir Animal
            // Obter próximo ID disponível para animal
            $stmtIdAnim = $pdo->query("SELECT MAX(cd_animal) as max_id FROM animal");
            $rowIdAnim = $stmtIdAnim->fetch(PDO::FETCH_ASSOC);
            $cd_animal = ($rowIdAnim['max_id'] ? $rowIdAnim['max_id'] : 0) + 1;

            $stmtInsertAnim = $pdo->prepare("INSERT INTO animal (cd_animal, nm_animal, cd_especie, id_user, id_plano) VALUES (:id, :nome, :especie, :user, :plano)");
            $stmtInsertAnim->bindParam(':id', $cd_animal);
            $stmtInsertAnim->bindParam(':nome', $nomeAnimal);
            $stmtInsertAnim->bindParam(':especie', $cd_especie);
            $stmtInsertAnim->bindParam(':user', $idUser);
            $stmtInsertAnim->bindParam(':plano', $idPlano);
            $stmtInsertAnim->execute();

            // 3. Lidar com Upload da Foto
            if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
                $targetDir = "images/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $nomeAnimal . ".png";
                move_uploaded_file($foto['tmp_name'], $targetFile);
            }
            
            return $cd_animal; // Retornar ID ao invés de verdadeiro

        } catch (PDOException $e) {
            echo 'Erro ao cadastrar: ' . $e->getMessage();
            return false;
        }
    }

    function RegistrarProntuario($idAnimal, $nomeTratamento, $data, $obs)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. Verificar/Inserir Tratamento
            // Precisamos encontrar se o tratamento existe para obter o ID, ou criar um novo.
            $stmt = $pdo->prepare("SELECT cd_tratamento FROM tratamento WHERE nm_tratamento = :nome");
            $stmt->bindParam(':nome', $nomeTratamento);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $cd_tratamento = $row['cd_tratamento'];
            } else {
                // Obter próximo ID disponível
                $stmtId = $pdo->query("SELECT MAX(cd_tratamento) as max_id FROM tratamento");
                $rowId = $stmtId->fetch(PDO::FETCH_ASSOC);
                $cd_tratamento = ($rowId['max_id'] ? $rowId['max_id'] : 0) + 1;

                $stmtInsert = $pdo->prepare("INSERT INTO tratamento (cd_tratamento, nm_tratamento, ds_tratamento) VALUES (:id, :nome, 'Criado automaticamente')");
                $stmtInsert->bindParam(':id', $cd_tratamento);
                $stmtInsert->bindParam(':nome', $nomeTratamento);
                $stmtInsert->execute();
            }

            // 2. Inserir no Prontuário
            // PK do Prontuário é (cd_animal, cd_tratamento, dt_tratamento)
            // Verificamos conflito ou apenas inserimos.
            
            // Formatar datahora se apenas data for passada
            if (strlen($data) <= 10) {
                $data .= ' ' . date('H:i:s');
            }

            $stmtInsertPront = $pdo->prepare("INSERT INTO prontuario (cd_animal, cd_tratamento, dt_tratamento, ds_observacao) VALUES (:animal, :tratamento, :data, :obs)");
            $stmtInsertPront->bindParam(':animal', $idAnimal);
            $stmtInsertPront->bindParam(':tratamento', $cd_tratamento);
            $stmtInsertPront->bindParam(':data', $data);
            $stmtInsertPront->bindParam(':obs', $obs);
            $stmtInsertPront->execute();
            
            return true;

        } catch (PDOException $e) {
            echo 'Erro ao registrar prontuário: ' . $e->getMessage();
            return false;
        }
    }

    function ExcluirTratamento($idAnimal, $nomeTratamento, $data)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Obter ID do Tratamento
            $stmt = $pdo->prepare("SELECT cd_tratamento FROM tratamento WHERE nm_tratamento = :nome");
            $stmt->bindParam(':nome', $nomeTratamento);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $cd_tratamento = $row['cd_tratamento'];
                
                $stmtDel = $pdo->prepare("DELETE FROM prontuario WHERE cd_animal = :animal AND cd_tratamento = :tratamento AND dt_tratamento = :data");
                $stmtDel->bindParam(':animal', $idAnimal);
                $stmtDel->bindParam(':tratamento', $cd_tratamento);
                $stmtDel->bindParam(':data', $data);
                $stmtDel->execute();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    function AtualizarTratamento($idAnimal, $oldData, $oldNomeTratamento, $newData, $newNomeTratamento, $newObs)
    {
         // Estratégia de atualização: Excluir registro antigo e inserir novo
         // Isso evita atualizações complexas de chave primária se a data mudar
         if ($this->ExcluirTratamento($idAnimal, $oldNomeTratamento, $oldData)) {
             return $this->RegistrarProntuario($idAnimal, $newNomeTratamento, $newData, $newObs);
         }
         return false;
    }

    function Excluir($id)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. Obter nome do animal para excluir imagem depois
            $stmtName = $pdo->prepare("SELECT nm_animal FROM animal WHERE cd_animal = :id");
            $stmtName->bindParam(':id', $id);
            $stmtName->execute();
            $animal = $stmtName->fetch(PDO::FETCH_ASSOC);

            // 2. Excluir do Prontuário (Chave Estrangeira)
            $stmtPront = $pdo->prepare("DELETE FROM prontuario WHERE cd_animal = :id");
            $stmtPront->bindParam(':id', $id);
            $stmtPront->execute();

            // 3. Excluir do Animal
            $stmtAnim = $pdo->prepare("DELETE FROM animal WHERE cd_animal = :id");
            $stmtAnim->bindParam(':id', $id);
            $stmtAnim->execute();

            // 4. Excluir Imagem
            if ($animal) {
                $imagePath = 'images/' . $animal['nm_animal'] . '.png';
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            return true;

        } catch (PDOException $e) {
            echo 'Erro ao excluir: ' . $e->getMessage();
            return false;
        }
    }

    function BuscarPeloId($id)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        try
         {
            
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cSQL = $pdo->prepare('
                SELECT a.cd_animal, a.nm_animal, a.cd_especie, a.id_user, a.id_plano 
                FROM animal a
                WHERE a.cd_animal = :id
            ');
            $cSQL->bindParam('id', $id);
            $cSQL->execute();

            if ($dados = $cSQL->fetch(PDO::FETCH_ASSOC))
            {
                $codigo = $dados['cd_animal'];
                $nome = $dados['nm_animal'];
                $codigoEspecie = $dados['cd_especie'];
                $idUser = $dados['id_user'];
                $idPlano = isset($dados['id_plano']) ? $dados['id_plano'] : null;

                $cSQL_Especie = $pdo->prepare('Select nm_especie from especie where cd_especie = :codigo');
                $cSQL_Especie->bindParam('codigo', $codigoEspecie);
                $cSQL_Especie->execute();

                $dadosEspecie = $cSQL_Especie->fetch(PDO::FETCH_ASSOC);
                $nomeEspecie = $dadosEspecie['nm_especie'];

                $especie = new Especie($codigoEspecie, $nomeEspecie);
                
                $animal = new Animal($codigo, $nome, $especie, $idUser, $idPlano);
                return $animal;
            }
            $pdo = null;
        } 
        catch (PDOException $e) 
        {
            echo 'Erro:' . $e->getMessage();
        }
        return null; // Return null if not found
    }

    function ListarProntuario($idAnimal)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        $lista = [];
        try
         {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = '
                SELECT p.dt_tratamento, t.nm_tratamento, p.ds_observacao
                FROM prontuario p
                INNER JOIN tratamento t ON p.cd_tratamento = t.cd_tratamento
                WHERE p.cd_animal = :idAnimal
                ORDER BY p.dt_tratamento DESC
            ';
            
            $cSQL = $pdo->prepare($sql);
            $cSQL->bindParam('idAnimal', $idAnimal);
            $cSQL->execute();

            while ($dados = $cSQL->fetch(PDO::FETCH_ASSOC))
            {
                // Assumindo estrutura da classe Prontuario baseada em arquivo visto anteriormente
                // Podemos precisar ajustar se a classe Prontuario for estritamente tipada ou tiver construtor diferente
                // Por enquanto, retornar um objeto ou array associativo está bem, mas vamos tentar usar a classe se possível.
                // Olhando para classes/models/prontuario.php: public $Animal; $Tratamento; $DataTratamento; $Descricao;
                
                // Vamos retornar um objeto anônimo ou array para simplicidade na view, 
                // ou instância de Prontuario se quisermos ser estritos. 
                // Dado que a view só precisa de strings, um objeto é mais fácil.
                
                $item = new stdClass();
                $item->DataTratamento = $dados['dt_tratamento'];
                $item->Tratamento = $dados['nm_tratamento'];
                $item->Descricao = $dados['ds_observacao'];
                
                array_push($lista, $item);
            }
            $pdo = null;
        } 
        catch (PDOException $e) 
        {
            echo 'Erro:' . $e->getMessage();
        }
        return $lista;
    }
    function GetPlanIdByName($planName)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Lidar com correspondências parciais se necessário, mas por enquanto exata ou contida
            // Já que usamos 'Amigo Fiel' na URL, vamos procurar correspondência exata primeiro
            $stmt = $pdo->prepare("SELECT cd_plano FROM planos WHERE nm_plano = :nome");
            $stmt->bindParam(':nome', $planName);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row['cd_plano'];
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    function GetTratamentosDoPlano($idPlano)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        $lista = [];
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $stmt = $pdo->prepare("
                SELECT t.nm_tratamento 
                FROM plano_tratamento pt
                JOIN tratamento t ON pt.cd_tratamento = t.cd_tratamento
                WHERE pt.cd_plano = :idPlano
            ");
            $stmt->bindParam(':idPlano', $idPlano);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lista[] = $row['nm_tratamento'];
            }
        } catch (PDOException $e) {}
        return $lista;
    }
}