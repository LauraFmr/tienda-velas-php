<?php
namespace services;

use models\Rol;
use PDO;

class RolesService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Devuelve todos los roles activos ordenados alfabéticamente
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM roles ORDER BY nombre ASC";
        $st = $this->pdo->query($sql);
        $rows = $st->fetchAll();
        return array_map(fn($r) => $this->mapRol($r), $rows);
    }

    /**
     * Busca un rol por su UUID
     */
    public function findById(string $id): ?Rol
    {
        $sql = "SELECT * FROM roles WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ? $this->mapRol($row) : null;
    }

    /**
     * Crea un nuevo rol
     */
    public function create(string $nombre, ?string $descripcion = null): bool
    {
        $sql = "INSERT INTO roles (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':nombre' => $nombre, ':descripcion' => $descripcion]);
    }

    /**
     * Actualiza un rol existente
     */
    public function update(string $id, string $nombre, ?string $descripcion = null): bool
    {
        $sql = "UPDATE roles 
                   SET nombre = :nombre, descripcion = :descripcion, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion
        ]);
    }

    /**
     * Elimina un rol (solo si no está asociado a usuarios)
     */
    public function delete(string $id): bool
    {
        // Comprueba si el rol está asignado a algún usuario
        $check = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios_roles WHERE rol_id = :id");
        $check->execute([':id' => $id]);
        if ($check->fetchColumn() > 0) {
            return false; // No borrar roles en uso
        }

        $st = $this->pdo->prepare("DELETE FROM roles WHERE id = :id");
        return $st->execute([':id' => $id]);
    }

    private function mapRol(array $r): Rol
    {
        $rol = new Rol();
        $rol->id = $r['id'];
        $rol->nombre = $r['nombre'];
        $rol->descripcion = $r['descripcion'];
        return $rol;
    }
}