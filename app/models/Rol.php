<?php
require_once __DIR__ . '/../core/Database.php';

class Rol
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getAllForRegister(): array
    {
        $sql = "SELECT idRol, nombre
                FROM Roles
                WHERE estado = 1 AND nombre != 'Administrador'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActives(): array
    {
        $sql = "SELECT idRol, nombre
                FROM Roles
                WHERE estado = 1
                ORDER BY nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>