<?php

class User {
    public $Id;
    public $Name;
    public $Email;
    public $Role;
    public $Provider;

    function __construct($id, $name, $email, $role, $provider) {
        $this->Id = $id;
        $this->Name = $name;
        $this->Email = $email;
        $this->Role = $role;
        $this->Provider = $provider;
    }
}
