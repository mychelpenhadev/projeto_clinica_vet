<?php

class AnimalView {

    function ExibirTodosAnimais ()
    {
        $animalController = new AnimalController();
        $listaTodosAnimais = $animalController->Listar();

        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
        $isAdminOrVet = ($userRole === 'admin' || $userRole === 'Veterinário');

        for($i=0; $i < count($listaTodosAnimais) ; $i++) {
            $isOwner = ($listaTodosAnimais[$i]->IdUser == $userId);
            $canAccess = ($isOwner || $isAdminOrVet);

            echo "<div class='caixaAnimal'>";
            
            if ($canAccess) {
                echo "<a href='atendimento.php?id={$listaTodosAnimais[$i]->Codigo}'>";
            }

            echo "   <img src='images/{$listaTodosAnimais[$i]->Nome}.png'>
                     <div>
                         <h1>{$listaTodosAnimais[$i]->Nome}</h1>
                         <p>{$listaTodosAnimais[$i]->Especie->Nome}</p>
                     </div>";

            if ($canAccess) {
                echo "</a>";
            }
            
            echo "</div>";
        }
    }

    function BuscarPeloNome($nome)
    {
        $animalController = new AnimalController();
        $listaAnimaisComEsteNome = $animalController->BuscarPeloNome($nome);
        if(count($listaAnimaisComEsteNome) == 0)
        {
            echo "<p>Não existem animais com esse nome em nossos sistemas</p>";
        }
        else
        {
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
            $isAdminOrVet = ($userRole === 'admin' || $userRole === 'Veterinário');

            for($i=0; $i < count($listaAnimaisComEsteNome) ; $i++) {
                $isOwner = ($listaAnimaisComEsteNome[$i]->IdUser == $userId);
                $canAccess = ($isOwner || $isAdminOrVet);

                echo "<div class='caixaAnimal'>";
                
                if ($canAccess) {
                    echo "<a href='atendimento.php?id={$listaAnimaisComEsteNome[$i]->Codigo}'>";
                }

                echo "    <img src='images/{$listaAnimaisComEsteNome[$i]->Nome}.png'>
                          <div>
                              <h1>{$listaAnimaisComEsteNome[$i]->Nome}</h1>
                              <p>{$listaAnimaisComEsteNome[$i]->Especie->Nome}</p>
                          </div>";

                if ($canAccess) {
                    echo "</a>";
                }

                echo "</div>";
            }
        }
    }
}