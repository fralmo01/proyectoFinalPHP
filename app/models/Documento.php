<?php
require_once __DIR__ . '/../core/Database.php';

class Documento
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function listarTipos(): array
    {
        $sql = "CALL sp_listar_tipo_documentos()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows ?: [];
    }

    public function upsertDocumento(int $idUsuario, int $idTipoDocumento, string $rutaArchivo, int $usuarioAud): ?array
    {
        $sql = "CALL sp_actualizar_documento(?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario, $idTipoDocumento, $rutaArchivo, $usuarioAud]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }


    public function listarPorUsuario(int $idUsuario): array
    {
        $sql = "CALL sp_documentos_listar_usuario(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows ?: [];
    }
}