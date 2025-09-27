<?php
require_once __DIR__ . '/../core/Database.php';

class Empresa
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function actualizarEmpresa($data)
    {
        $sql = "CALL sp_actualizar_empresa(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['idEmpresa'],
            $data['nombre'],
            $data['direccion'],
            $data['telefono'],
            $data['email'],
            $data['sitioWeb'],
            $data['logoEmpresa']
        ]);
        return $stmt->fetch();
    }

    public function getEmpresaById($idEmpresa)
    {
        $sql = "SELECT * FROM Empresas WHERE idEmpresa = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEmpresa]);
        return $stmt->fetch();
    }

    public function listarActivas(): array
    {
        $sql = "SELECT idEmpresa, nombre
                FROM Empresas
                WHERE estado = 1
                ORDER BY nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}