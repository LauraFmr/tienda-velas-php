<?php

namespace models;


class Categoria
{
    public $id;
    public $nombre;
    public $descripcion;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;


    public function __construct($id = null, $nombre = null, $descripcion = null, $createdAt = null, $updatedAt = null, $isDeleted = false)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }
}
