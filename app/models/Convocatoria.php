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

    public function listarActivas(array $filtros = [])
    {
        /* BORRRA ESTE PROCEDMIMIENTO ALMACENADO
        $sql = "CALL sp_listar_convocatorias_activas()";
        */
        $sql = "SELECT 
                    c.idConvocatoria,
                    c.titulo,
                    c.descripcion,
                    c.fechaInicio,
                    c.fechaFin,
                    c.estado,
                    c.idEmpresa,
                    c.idJornada,
                    c.idModalidad,
                    e.nombre AS empresaNombre,
                    e.logoEmpresa,
                    j.nombreJornada,
                    m.nombreModalidad
                FROM Convocatorias c
                INNER JOIN Empresas e ON c.idEmpresa = e.idEmpresa
                INNER JOIN Jornadas j ON c.idJornada = j.idJornada
                INNER JOIN Modalidades m ON c.idModalidad = m.idModalidad
                WHERE c.estado = 1
                  AND c.fechaFin >= CURDATE()";

        $params = [];

        if (!empty($filtros['idModalidad'])) {
            $sql .= " AND c.idModalidad = ?";
            $params[] = $filtros['idModalidad'];
        }

        if (!empty($filtros['idJornada'])) {
            $sql .= " AND c.idJornada = ?";
            $params[] = $filtros['idJornada'];
        }

        $sql .= " ORDER BY c.fechaCreacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows ?: [];
    }

    public function listarParaAdmin(array $filtros = []): array
    {
        $sql = "SELECT
                    c.idConvocatoria,
                    c.titulo,
                    c.descripcion,
                    c.fechaInicio,
                    c.fechaFin,
                    c.estado,
                    c.fechaCreacion,
                    e.nombre AS empresaNombre,
                    e.logoEmpresa,
                    j.nombreJornada,
                    m.nombreModalidad
                FROM Convocatorias c
                INNER JOIN Empresas e ON c.idEmpresa = e.idEmpresa
                INNER JOIN Jornadas j ON c.idJornada = j.idJornada
                INNER JOIN Modalidades m ON c.idModalidad = m.idModalidad
                WHERE 1 = 1";

        $params = [];

        if (!empty($filtros['idModalidad'])) {
            $sql .= " AND c.idModalidad = ?";
            $params[] = $filtros['idModalidad'];
        }

        if (!empty($filtros['idJornada'])) {
            $sql .= " AND c.idJornada = ?";
            $params[] = $filtros['idJornada'];
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '' && $filtros['estado'] !== null) {
            $sql .= " AND c.estado = ?";
            $params[] = (int) $filtros['estado'];
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (c.titulo LIKE ? OR e.nombre LIKE ?)";
            $termino = '%' . $filtros['buscar'] . '%';
            $params[] = $termino;
            $params[] = $termino;
        }

        $sql .= " ORDER BY c.fechaCreacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
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

    public function cambiarEstado(int $idConvocatoria, int $estado, ?int $idUsuario): bool
    {
        $estadoNormalizado = $estado === 1 ? 1 : 0;

        $sql = "UPDATE Convocatorias
                SET estado = ?,
                    usuarioActualizacion = ?,
                    fechaActualizacion = NOW()
                WHERE idConvocatoria = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$estadoNormalizado, $idUsuario, $idConvocatoria]);

        return $stmt->rowCount() > 0;
    }
}
