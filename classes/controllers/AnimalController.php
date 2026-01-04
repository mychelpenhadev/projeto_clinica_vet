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
            $cSQL = $pdo->prepare('select cd_animal, nm_animal, cd_especie from animal');
            $cSQL->execute();

            while ($dados = $cSQL->fetch(PDO::FETCH_ASSOC))
            {
                $codigo = $dados['cd_animal'];
                $nome = $dados['nm_animal'];
                $codigoEspecie = $dados['cd_especie'];

                $cSQL_Especie = $pdo->prepare('Select nm_especie from especie where cd_especie = :codigo');
                $cSQL_Especie->bindParam('codigo', $codigoEspecie);
                $cSQL_Especie->execute();

                $dadosEspecie = $cSQL_Especie->fetch(PDO::FETCH_ASSOC);
                $nomeEspecie = $dadosEspecie['nm_especie'];

                $especie = new Especie($codigoEspecie, $nomeEspecie);
                
                $animal = new Animal($codigo, $nome, $especie);
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
                SELECT a.cd_animal, a.nm_animal, a.cd_especie 
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

                $cSQL_Especie = $pdo->prepare('Select nm_especie from especie where cd_especie = :codigo');
                $cSQL_Especie->bindParam('codigo', $codigoEspecie);
                $cSQL_Especie->execute();

                $dadosEspecie = $cSQL_Especie->fetch(PDO::FETCH_ASSOC);
                $nomeEspecie = $dadosEspecie['nm_especie'];

                $especie = new Especie($codigoEspecie, $nomeEspecie);
                
                $animal = new Animal($codigo, $nome, $especie);
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
    function Cadastrar($nomeAnimal, $nomeEspecie)
    {
        $servidor = 'mysql:host=localhost;dbname=prontuario_vet';
        $usuario = 'root';
        $senha = '';
        
        try {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. Check/Insert Especie
            $stmt = $pdo->prepare("SELECT cd_especie FROM especie WHERE nm_especie = :especie");
            $stmt->bindParam(':especie', $nomeEspecie);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $cd_especie = $row['cd_especie'];
            } else {
                // Get next available ID for especie (manual auto-increment style since sql script didn't set auto_increment)
                $stmtId = $pdo->query("SELECT MAX(cd_especie) as max_id FROM especie");
                $rowId = $stmtId->fetch(PDO::FETCH_ASSOC);
                $cd_especie = ($rowId['max_id'] ? $rowId['max_id'] : 0) + 1;

                $stmtInsertEsp = $pdo->prepare("INSERT INTO especie (cd_especie, nm_especie) VALUES (:id, :nome)");
                $stmtInsertEsp->bindParam(':id', $cd_especie);
                $stmtInsertEsp->bindParam(':nome', $nomeEspecie);
                $stmtInsertEsp->execute();
            }

            // 2. Insert Animal
            // Get next available ID for animal
            $stmtIdAnim = $pdo->query("SELECT MAX(cd_animal) as max_id FROM animal");
            $rowIdAnim = $stmtIdAnim->fetch(PDO::FETCH_ASSOC);
            $cd_animal = ($rowIdAnim['max_id'] ? $rowIdAnim['max_id'] : 0) + 1;

            $stmtInsertAnim = $pdo->prepare("INSERT INTO animal (cd_animal, nm_animal, cd_especie) VALUES (:id, :nome, :especie)");
            $stmtInsertAnim->bindParam(':id', $cd_animal);
            $stmtInsertAnim->bindParam(':nome', $nomeAnimal);
            $stmtInsertAnim->bindParam(':especie', $cd_especie);
            $stmtInsertAnim->execute();
            
            return true;

        } catch (PDOException $e) {
            echo 'Erro ao cadastrar: ' . $e->getMessage();
            return false;
        }
    }
}