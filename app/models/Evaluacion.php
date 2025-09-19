<?php
require_once __DIR__ . '/../core/Database.php';

class Evaluacion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function listarPorEmpresa($idEmpresa)
    {
        $sql = "SELECT 
                    e.idEvaluacion,
                    e.puntaje,
                    e.observaciones,
                    e.fechaEvaluacion,
                    p.idPostulacion,
                    p.fechaPostulacion,
                    c.titulo AS convocatoria,
                    CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellidoPaterno, ''), ' ', IFNULL(u.apellidoMaterno, '')) AS postulante,
                    u.email,
                    u.telefono,
                    et.nombre AS etapa,
                    r.fechaResultado,
                    er.nombre AS estadoResultado
                FROM Evaluaciones e
                INNER JOIN Postulaciones p ON e.idPostulacion = p.idPostulacion
                INNER JOIN Convocatorias c ON p.idConvocatoria = c.idConvocatoria
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                LEFT JOIN Etapas et ON p.idEtapa = et.idEtapa
                LEFT JOIN Resultados r ON p.idPostulacion = r.idPostulacion
                LEFT JOIN EstadoResultados er ON r.idEstadoResultado = er.idEstadoResultado
                WHERE c.idEmpresa = ?
                ORDER BY e.fechaEvaluacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEmpresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>