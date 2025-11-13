<?php

namespace models;


class Producto
{
    public $id;
    public $uuid;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $imagen;
    public $categoriaId;
    public $categoriaNombre;
    public $fraganciaId;
    public $fraganciaNombre;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;
}
