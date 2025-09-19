<?php
require_once __DIR__ . '/../core/Database.php';

class Evaluacion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function obtenerPostulacionesPorEmpresa(int $idEmpresa): array
    {
        $sql = "SELECT
                    p.idPostulacion,
                    p.idUsuario,
                    p.idEtapa,
                    et.nombre AS etapa,
                    p.fechaPostulacion,
                    c.idConvocatoria,
                    c.titulo AS convocatoria,
                    CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellidoPaterno, ''), ' ', IFNULL(u.apellidoMaterno, '')) AS postulante,
                    r.idResultado,
                    r.idEstadoResultado,
                    er.nombre AS estadoResultado
                    FROM Postulaciones p
                INNER JOIN Convocatorias c ON p.idConvocatoria = c.idConvocatoria
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                LEFT JOIN Etapas et ON p.idEtapa = et.idEtapa
                LEFT JOIN Resultados r ON r.idPostulacion = p.idPostulacion
                LEFT JOIN EstadoResultados er ON r.idEstadoResultado = er.idEstadoResultado
                WHERE c.idEmpresa = ?
                ORDER BY p.fechaPostulacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEmpresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function perteneceAEmpresa(int $idPostulacion, int $idEmpresa): bool
    {
        $sql = "SELECT COUNT(*) AS total
                FROM Postulaciones p
                INNER JOIN Convocatorias c ON p.idConvocatoria = c.idConvocatoria
                WHERE p.idPostulacion = ? AND c.idEmpresa = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion, $idEmpresa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['total'] ?? 0) > 0;
    }

    public function obtenerDetallePostulacion(int $idPostulacion): ?array
    {
        $sql = "SELECT
                    p.idPostulacion,
                    p.idUsuario,
                    p.idEtapa,
                    p.comentario,
                    p.fechaPostulacion,
                    c.titulo AS convocatoria,
                    c.descripcion AS descripcionConvocatoria,
                    c.idConvocatoria,
                    et.nombre AS etapa,
                    CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellidoPaterno, ''), ' ', IFNULL(u.apellidoMaterno, '')) AS postulante,
                    u.email,
                    u.telefono
                FROM Postulaciones p
                INNER JOIN Convocatorias c ON p.idConvocatoria = c.idConvocatoria
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                LEFT JOIN Etapas et ON p.idEtapa = et.idEtapa
                WHERE p.idPostulacion = ?
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function registrarEvaluacion(int $idPostulacion, float $puntaje, ?string $observaciones, int $idUsuario): int
    {
        $sql = "INSERT INTO Evaluaciones (idPostulacion, puntaje, observaciones, usuarioCreacion)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion, $puntaje, $observaciones, $idUsuario]);
        return (int)$this->pdo->lastInsertId();
    }

    public function obtenerEvaluaciones(int $idPostulacion): array
    {
        $sql = "SELECT
                    e.idEvaluacion,
                    e.puntaje,
                    e.observaciones,
                    e.fechaEvaluacion,
                    e.usuarioCreacion,
                    CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellidoPaterno, ''), ' ', IFNULL(u.apellidoMaterno, '')) AS evaluador
                FROM Evaluaciones e
                LEFT JOIN Usuarios u ON e.usuarioCreacion = u.idUsuario
                WHERE e.idPostulacion = ?
                ORDER BY e.fechaEvaluacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialAcciones(int $idPostulacion): array
    {
        $sql = "SELECT
                    h.idAccion,
                    h.accion,
                    h.tablaAfectada,
                    h.fechaAccion,
                    h.idUsuario,
                    CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellidoPaterno, ''), ' ', IFNULL(u.apellidoMaterno, '')) AS usuario
                FROM HistorialAcciones h
                LEFT JOIN Usuarios u ON h.idUsuario = u.idUsuario
                WHERE h.tablaAfectada = 'Postulaciones' AND h.idRegistro = ?
                ORDER BY h.fechaAccion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEtapa(int $idPostulacion, int $idEtapa, int $idUsuario): bool
    {
        $sql = "UPDATE Postulaciones
                SET idEtapa = ?, fechaActualizacion = NOW(), usuarioActualizacion = ?
                WHERE idPostulacion = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEtapa, $idUsuario, $idPostulacion]);
        return $stmt->rowCount() > 0;
    }

    public function obtenerEtapaPorId(int $idEtapa): ?array
    {
        $sql = "SELECT idEtapa, nombre FROM Etapas WHERE idEtapa = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEtapa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function obtenerSiguienteEtapa(int $idEtapaActual): ?array
    {
        $sql = "SELECT idEtapa, nombre
                FROM Etapas
                WHERE estado = 1 AND idEtapa > ?
                ORDER BY idEtapa ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEtapaActual]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function obtenerEtapasPosteriores(int $idEtapaActual): array
    {
        $sql = "SELECT idEtapa, nombre
                FROM Etapas
                WHERE estado = 1 AND idEtapa > ?
                ORDER BY idEtapa ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEtapaActual]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEtapas(): array
    {
        $sql = "SELECT idEtapa, nombre
                FROM Etapas
                WHERE estado = 1
                ORDER BY idEtapa ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstadosResultado(): array
    {
        $sql = "SELECT idEstadoResultado, nombre
                FROM EstadoResultados
                WHERE estado = 1
                ORDER BY nombre";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarResultado(int $idPostulacion, int $idEstadoResultado, int $idUsuario): int
    {
        $sql = "SELECT idResultado FROM Resultados WHERE idPostulacion = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $sqlUpdate = "UPDATE Resultados
                           SET idEstadoResultado = ?, fechaResultado = NOW(), usuarioActualizacion = ?, fechaActualizacion = NOW()
                           WHERE idResultado = ?";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$idEstadoResultado, $idUsuario, $row['idResultado']]);
            return (int)$row['idResultado'];
        }

        $sqlInsert = "INSERT INTO Resultados (idPostulacion, idEstadoResultado, usuarioCreacion)
                      VALUES (?, ?, ?)";
        $stmtInsert = $this->pdo->prepare($sqlInsert);
        $stmtInsert->execute([$idPostulacion, $idEstadoResultado, $idUsuario]);
        return (int)$this->pdo->lastInsertId();
    }

    public function obtenerResultado(int $idPostulacion): ?array
    {
        $sql = "SELECT r.idResultado,
                       r.idEstadoResultado,
                       r.fechaResultado,
                       er.nombre AS estado,
                       r.usuarioCreacion,
                       r.usuarioActualizacion
                FROM Resultados r
                INNER JOIN EstadoResultados er ON r.idEstadoResultado = er.idEstadoResultado
                WHERE r.idPostulacion = ?
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idPostulacion]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

?>