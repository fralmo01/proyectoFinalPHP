<?php
require_once __DIR__ . '/../core/Database.php';

class Historial
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function obtenerHistorial(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT h.idAccion,
                       h.idUsuario,
                       TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidoPaterno, ''), ' ', COALESCE(u.apellidoMaterno, ''))) AS nombreUsuario,
                       h.accion,
                       h.tablaAfectada,
                       h.idRegistro,
                       h.fechaAccion
                  FROM HistorialAcciones h
             LEFT JOIN Usuarios u ON h.idUsuario = u.idUsuario
                 WHERE 1 = 1";

        $params = [];

        if ($fechaInicio !== null) {
            $sql .= " AND h.fechaAccion >= ?";
            $params[] = $fechaInicio . ' 00:00:00';
        }

        if ($fechaFin !== null) {
            $sql .= " AND h.fechaAccion <= ?";
            $params[] = $fechaFin . ' 23:59:59';
        }

        $sql .= " ORDER BY h.fechaAccion DESC, h.idAccion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAccionesPorUsuario(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT COALESCE(NULLIF(TRIM(CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidoPaterno, ''), ' ', COALESCE(u.apellidoMaterno, ''))), ''), 'Sin usuario') AS nombreUsuario,
                       COUNT(*) AS totalAcciones
                  FROM HistorialAcciones h
             LEFT JOIN Usuarios u ON h.idUsuario = u.idUsuario
                 WHERE 1 = 1";

        $params = [];

        if ($fechaInicio !== null) {
            $sql .= " AND h.fechaAccion >= ?";
            $params[] = $fechaInicio . ' 00:00:00';
        }

        if ($fechaFin !== null) {
            $sql .= " AND h.fechaAccion <= ?";
            $params[] = $fechaFin . ' 23:59:59';
        }

        $sql .= " GROUP BY nombreUsuario
                   ORDER BY totalAcciones DESC, nombreUsuario ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}