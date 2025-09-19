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

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarJornadas()
    {
        $sql = "SELECT idJornada, nombreJornada FROM Jornadas WHERE estado = 1";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarModalidades()
    {
        $sql = "SELECT idModalidad, nombreModalidad FROM Modalidades WHERE estado = 1";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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

    public function listarActivas()
    {
        $sql = "CALL sp_listar_convocatorias_activas()";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows ?: [];
    }

    public function obtenerPorId($idConvocatoria, $idEmpresa)
    {
        $sql = "SELECT c.idConvocatoria, c.titulo, c.descripcion, c.fechaInicio, c.fechaFin,
                       c.idJornada, c.idModalidad, c.estado,
                       j.nombreJornada, m.nombreModalidad
                FROM Convocatorias c
                INNER JOIN Jornadas j ON c.idJornada = j.idJornada
                INNER JOIN Modalidades m ON c.idModalidad = m.idModalidad
                WHERE c.idConvocatoria = ? AND c.idEmpresa = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idConvocatoria, $idEmpresa]);
        $convocatoria = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $convocatoria ?: null;
    }

    public function actualizar($data)
    {
        $sql = "UPDATE Convocatorias
                SET titulo = ?,
                    descripcion = ?,
                    fechaInicio = ?,
                    fechaFin = ?,
                    idJornada = ?,
                    idModalidad = ?,
                    usuarioActualizacion = ?,
                    fechaActualizacion = NOW()
                WHERE idConvocatoria = ? AND idEmpresa = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'],
            $data['fechaInicio'],
            $data['fechaFin'],
            $data['idJornada'],
            $data['idModalidad'],
            $data['usuarioActualizacion'],
            $data['idConvocatoria'],
            $data['idEmpresa']
        ]);

        return $stmt->rowCount() >= 0;
    }

    public function eliminarLogico($idConvocatoria, $idEmpresa, $idUsuario)
    {
        $sql = "UPDATE Convocatorias
                SET estado = 0,
                    usuarioActualizacion = ?,
                    fechaActualizacion = NOW()
                WHERE idConvocatoria = ? AND idEmpresa = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario, $idConvocatoria, $idEmpresa]);

        return $stmt->rowCount() > 0;
    }
}
