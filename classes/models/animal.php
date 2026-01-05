<?php

class Animal {
    
    public $Codigo;
    public $Nome;
    public $Especie;
    public $IdUser;
    public $IdPlano;

    function __construct($codigo = null, $nome = null, Especie $especie = null, $idUser = null, $idPlano = null)
    {
      $this->Codigo = $codigo;
      $this->Nome = $nome;
      if($especie != null)
        $this->Especie = $especie;
      else
        $this->Especie = new Especie();
      
      $this->IdUser = $idUser;
      $this->IdPlano = $idPlano;
    }
}