<?php

namespace models;


class User
{
    public $id;
    public $nombre;
    public $apellido;
    public $username;
    public $email;
    public $password;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;
    public $roles = []; // roles en array
}

/*
//constructor para iniciaclizar

public function __construct($id, $nombre, $apellido, $username, $email, $password, $createdAt, $updatedAt, $isDeleted) {

    
    $this->id = $id;
    $this->nombre = $nombre;
    $this->apellido = $apellido;
    $this->username = $username;
    $this->email = $email;
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->isDeleted = $isDeleted;  
    $this->roles = [];
    
}
*/
