<?php
require_once __DIR__ . '/../core/Database.php';

class Postulacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function postular($idUsuario, $idConvocatoria, $comentario = "") {
        $sql = "CALL sp_postular(?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario, $idConvocatoria, $comentario, $idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarPorUsuario($idUsuario) {
        $sql = "CALL sp_listar_postulaciones_usuario(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

