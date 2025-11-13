<?php

namespace config;


use Dotenv\Dotenv;
use PDO;
use PDOException;


class Config
{
    private static $instance; // Singleton


    public $db; // PDO


    public $rootPath; // Raíz del proyecto montado en /var/www/html
    public $uploadPath; // Ruta física
    public $uploadUrl; // URL pública


    private function __construct()
    {
        $this->rootPath = '/var/www/html';


        // Cargar .env desde la raíz del proyecto
        $dotenv = Dotenv::createImmutable($this->rootPath);
        $dotenv->load();


        $host = $_ENV['POSTGRES_HOST'] ?? 'postgres-db';
        $port = $_ENV['POSTGRES_PORT'] ?? '5432';
        $db = $_ENV['POSTGRES_DB'] ?? 'tienda_velas';
        $user = $_ENV['POSTGRES_USER'] ?? 'admin';
        $pass = $_ENV['POSTGRES_PASSWORD'] ?? 'adminPassword123';


        $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
        try {
            $this->db = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('DB connection error: ' . $e->getMessage());
        }


        $appBase = rtrim($_ENV['APP_BASE'] ?? 'http://localhost:8080', '/');
        $this->uploadPath = $this->rootPath . '/src/uploads/';
        $this->uploadUrl = $appBase . '/uploads/';
    }


    public static function getInstance(): Config
    {
        if (!self::$instance) self::$instance = new Config();
        return self::$instance;
    }

    /**
     * @property-read PDO $db
     * @property-read string $rootPath
     * @property-read string $uploadPath
     * @property-read string $uploadUrl
     */
    public function __get($name)
    {
        return $this->$name;
    }
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
