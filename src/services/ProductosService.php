<?php

namespace services;


use models\Producto;
use PDO;
use Ramsey\Uuid\Uuid;


class ProductosService
{
    private $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function findAllWithFilters(?string $q = null, ?string $categoriaId = null, ?string $fraganciaId = null): array
    {
        $sql = 'SELECT p.*, c.nombre AS categoria_nombre, f.nombre AS fragancia_nombre
FROM productos p
LEFT JOIN categorias c ON c.id = p.categoria_id
LEFT JOIN fragancias f ON f.id = p.fragancia_id
WHERE p.is_deleted = FALSE';
        $params = [];
        if ($q) {
            $sql .= ' AND (LOWER(p.nombre) LIKE :q OR LOWER(p.descripcion) LIKE :q)';
            $params[':q'] = '%' . strtolower($q) . '%';
        }
        if ($categoriaId) {
            $sql .= ' AND p.categoria_id = :cid';
            $params[':cid'] = $categoriaId;
        }
        if ($fraganciaId) {
            $sql .= ' AND p.fragancia_id = :fid';
            $params[':fid'] = $fraganciaId;
        }
        $sql .= ' ORDER BY p.created_at DESC';
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return array_map([$this, 'mapProducto'], $st->fetchAll());
    }

    public function findById(int $id): ?Producto
    {
        $st = $this->db->prepare('SELECT p.*, c.nombre AS categoria_nombre, f.nombre AS fragancia_nombre
FROM productos p
LEFT JOIN categorias c ON c.id=p.categoria_id
LEFT JOIN fragancias f ON f.id=p.fragancia_id
WHERE p.id=:id');
        $st->execute([':id' => $id]);
        $r = $st->fetch();
        return $r ? $this->mapProducto($r) : null;
    }


    public function save(array $data): int
    {
        $uuid = Uuid::uuid4()->toString();
        $sql = 'INSERT INTO productos (uuid,nombre,descripcion,precio,stock,imagen,categoria_id,fragancia_id)
VALUES (:uuid,:nombre,:descripcion,:precio,:stock,:imagen,:categoria_id,:fragancia_id)
RETURNING id';
        $st = $this->db->prepare($sql);
        $st->execute([
            ':uuid' => $uuid,
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio' => $data['precio'],
            ':stock' => $data['stock'],
            ':imagen' => $data['imagen'] ?? null,
            ':categoria_id' => $data['categoria_id'] ?: null,
            ':fragancia_id' => $data['fragancia_id'] ?: null,
        ]);
        return (int)$st->fetchColumn();
    }


    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE productos SET nombre=:nombre, descripcion=:descripcion, precio=:precio, stock=:stock,
categoria_id=:categoria_id, fragancia_id=:fragancia_id, updated_at=CURRENT_TIMESTAMP WHERE id=:id';
        $st = $this->db->prepare($sql);
        return $st->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio' => $data['precio'],
            ':stock' => $data['stock'],
            ':categoria_id' => $data['categoria_id'] ?: null,
            ':fragancia_id' => $data['fragancia_id'] ?: null,
        ]);
    }


    public function updateImage(int $id, ?string $imagenUrl): bool
    {
        $st = $this->db->prepare('UPDATE productos SET imagen=:img, updated_at=CURRENT_TIMESTAMP WHERE id=:id');
        return $st->execute([':img' => $imagenUrl, ':id' => $id]);
    }


    public function deleteById(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM productos WHERE id=:id');
        return $st->execute([':id' => $id]);
    }


    private function mapProducto(array $r): Producto
    {
        $p = new Producto();
        foreach ($r as $k => $v) {
            $key = $this->snakeToCamel($k);
            if (property_exists($p, $key)) $p->$key = $v;
        }
        $p->categoriaNombre = $r['categoria_nombre'] ?? null;
        $p->fraganciaNombre = $r['fragancia_nombre'] ?? null;
        return $p;
    }


    private function snakeToCamel(string $s): string
    {
        return preg_replace_callback('/_([a-z])/', fn($m) => strtoupper($m[1]), $s);
    }
}
