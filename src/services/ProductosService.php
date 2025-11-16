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
        // Empezamos escribiendo el texto de la consulta SQL (lo que le vamos a decir a la base de datos)
        $sql = 'SELECT p.*, c.nombre AS categoria_nombre, f.nombre AS fragancia_nombre
        FROM productos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN fragancias f ON f.id = p.fragancia_id
        WHERE p.is_deleted = FALSE';

        // Aquí guardaremos los valores que sustituirán a los "huecos" (:q, :cid, :fid) en la consulta
        $params = [];

        // Si $q tiene algún valor (no es null ni cadena vacía)
        if ($q) {
            // Añadimos más filtro al SQL: buscamos por nombre o descripción que contenga el texto de $q (en minúsculas)
            $sql .= ' AND (LOWER(p.nombre) LIKE :q OR LOWER(p.descripcion) LIKE :q)';
            // Guardamos el valor para :q, con los % para el "contiene"
            $params[':q'] = '%' . strtolower($q) . '%';
        }

        // Si $categoriaId tiene valor, filtramos por esa categoría
        if ($categoriaId) {
            $sql .= ' AND p.categoria_id = :cid';
            $params[':cid'] = $categoriaId;
        }

        // Si $fraganciaId tiene valor, filtramos por esa fragancia
        if ($fraganciaId) {
            $sql .= ' AND p.fragancia_id = :fid';
            $params[':fid'] = $fraganciaId;
        }

        // Añadimos el orden: primero los productos más nuevos (created_at DESC)
        $sql .= ' ORDER BY p.created_at DESC';

        // Preparamos la consulta en la base de datos
        $st = $this->db->prepare($sql);

        // Ejecutamos la consulta pasando los parámetros (si no hay, pasa un array vacío)
        $st->execute($params);

        // 1) $st->fetchAll() trae TODAS las filas de la BD en un array
        // 2) array_map aplica $this->mapProducto a cada fila
        // 3) devuelve un array nuevo con el resultado de mapProducto para cada fila
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
        $r = $st->fetch(); // devuelveme todo sobre el campo, toda la fila
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
        return (int) $st->fetchColumn(); // fetchColumn devuelve el valor de la primera columna de la primera fila
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
        // Instancio el producto
        $p = new Producto();
        // Recorro la filas del resultado y asigno las propiedades
        foreach ($r as $k => $v) {
            // Convierte snake_case a camelCase ej: categoria_nombre -> categoriaNombre
            $key = $this->snakeToCamel($k);
            // Asigno la propiedad si existe
            if (property_exists($p, $key))
                $p->$key = $v;
        }
        // Propiedades adicionales
        $p->categoriaNombre = $r['categoria_nombre'] ?? null;
        $p->fraganciaNombre = $r['fragancia_nombre'] ?? null;
        // Retorno el producto mapeado final 
        return $p;
    }


    private function snakeToCamel(string $s): string
    {
        return preg_replace_callback('/_([a-z])/', fn($m) => strtoupper($m[1]), $s);
    }
}
