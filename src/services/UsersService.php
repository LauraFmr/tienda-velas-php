<?php

namespace services;


use PDO;
use Exception;
use models\User;


class UsersService
{
    private $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    

    public function authenticate(string $username, string $password): User
    {
        // aqui iria la logica para verificar el nombre de usuario y la contraseña
        //por ejemplo, buscar en la base de datos y comparar la contraseña hasheada
        //supongamos que ya tienes una funcion que verifica la contraseña bcrypt

        // ejemplo de busqueda de usuario y verificacion de contraseña
        $user = $this->findUserByUsername($username);
        if (!$user || !password_verify($password, $user->password)) {
            //lanza una excepcion si no se encuentra el usuario o la contraseña es incorrecta
            throw new Exception('Usuario o contraseña incorrectos');
        }
        return $user;
    }


    public function findUserByUsername(string $username): ?User
    {
        // buscar el usuario por username en la base de datos
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE username = :u AND is_deleted = FALSE LIMIT 1');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
/*
$stm = $this..... (foto)


*/ 
        if (!$row) return null;

        $user = new User();
        $user->id = $row['id'];
        $user->nombre = $row['nombre'];
        $user->apellido = $row['apellido'];
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->createdAt = $row['created_at'];
        $user->updatedAt = $row['updated_at'];
        $user->isDeleted = $row['is_deleted'];
        $user->roles = $this->getRoles($user->id);
        return $user;
    }


    private function getRoles(string $userId): array
    {
        $sql = 'SELECT r.nombre FROM usuarios_roles ur JOIN roles r ON r.id = ur.rol_id WHERE ur.usuario_id = :id';
        $st = $this->db->prepare($sql);
        $st->execute([':id' => $userId]);
        return array_map(fn($r) => $r['nombre'], $st->fetchAll()); // aqui ella pone pdo::fetch_column
    }
}
