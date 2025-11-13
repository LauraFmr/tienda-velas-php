<?php

namespace services;


use models\Fragancia;
use PDO;


class FraganciasService
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function findAll(): array
    {
        $st = $this->pdo->query('SELECT * FROM fragancias WHERE is_deleted=FALSE ORDER BY nombre ASC');
        return array_map(fn($r) => new Fragancia($r['id'], $r['nombre'], $r['notas'], $r['created_at'], $r['updated_at'], $r['is_deleted']), $st->fetchAll());
    }
}
