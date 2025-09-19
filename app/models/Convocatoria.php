<?php
require_once __DIR__ . '/../core/Database.php';

class Convocatoria
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function crear($data)
    {
        $sql = "CALL sp_crear_convocatoria(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'],
            $data['fechaInicio'],
            $data['fechaFin'],
            $data['idEmpresa'],
            $data['idJornada'],
            $data['idModalidad'],
            $data['usuarioCreacion']
        ]);
        return $stmt->fetch();
    }

    public function listarJornadas()
    {
        $sql = "SELECT idJornada, nombreJornada FROM Jornadas WHERE estado = 1";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarModalidades()
    {
        $sql = "SELECT idModalidad, nombreModalidad FROM Modalidades WHERE estado = 1";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarPorEmpresa($idEmpresa)
    {
        $sql = "CALL sp_listar_convocatorias_empresa(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEmpresa]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows ?: [];
    }

    public function listarActivas() {
    $sql = "CALL sp_listar_convocatorias_activas()";
    $stmt = $this->pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $rows ?: [];
}








}
