<?php

namespace models;


class Fragancia
{
    public $id;
    public $nombre;
    public $notas;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;
    public function __construct($id = null, $nombre = null, $notas = null, $createdAt = null, $updatedAt = null, $isDeleted = false)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->notas = $notas;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }
}
