<?php

namespace services;

use models\Categoria;
use PDO;


class CategoriasService
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function findAll(): array
    {
        $st = $this->pdo->query('SELECT * FROM categorias WHERE is_deleted=FALSE ORDER BY nombre ASC');
        return array_map(function ($r) {
            return new Categoria($r['id'], $r['nombre'], $r['descripcion'], $r['created_at'], $r['updated_at'], $r['is_deleted']);
        }, $st->fetchAll());
    }
}
