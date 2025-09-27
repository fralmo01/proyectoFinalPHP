<?php
require_once __DIR__ . '/../core/Database.php';

class Empresa
{
    private $pdo;
    private ?bool $tieneColumnaCategoria = null;

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

    private function hasCategoriaColumn(): bool
    {
        if ($this->tieneColumnaCategoria === null) {
            try {
                $stmt = $this->pdo->query("SHOW COLUMNS FROM Empresas LIKE 'categoria'");
                $this->tieneColumnaCategoria = $stmt->fetch() ? true : false;
            } catch (PDOException $e) {
                $this->tieneColumnaCategoria = false;
            }
        }

        return $this->tieneColumnaCategoria;
    }

    public function listarEmpresas(string $categoriaFiltro = 'todas', string $estadoFiltro = 'activas'): array
    {
        $tieneCategoria = $this->hasCategoriaColumn();

        $sql = "SELECT idEmpresa, nombre, direccion, telefono, email, sitioWeb, logoEmpresa, estado, fechaCreacion";
        if ($tieneCategoria) {
            $sql .= ", COALESCE(categoria, 'Sin categoría') AS categoria";
        } else {
            $sql .= ", 'Sin categoría' AS categoria";
        }

        $sql .= " FROM Empresas WHERE 1 = 1";

        $params = [];

        if ($estadoFiltro === 'activos') {
            $sql .= " AND estado = 1";
        } elseif ($estadoFiltro === 'inactivos') {
            $sql .= " AND estado = 0";
        }

        if ($tieneCategoria && $categoriaFiltro !== 'todas') {
            $sql .= " AND categoria = ?";
            $params[] = $categoriaFiltro;
        }

        $sql .= " ORDER BY nombre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias(): array
    {
        if (!$this->hasCategoriaColumn()) {
            return [];
        }

        $sql = "SELECT DISTINCT categoria
                FROM Empresas
                WHERE categoria IS NOT NULL AND categoria <> ''
                ORDER BY categoria";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return array_map(static function ($row) {
            return $row['categoria'];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function actualizarEstado(int $idEmpresa, int $estado, ?int $usuarioAuditoria = null): bool
    {
        $sql = "UPDATE Empresas
                SET estado = ?,
                    fechaActualizacion = NOW(),
                    usuarioActualizacion = ?
                WHERE idEmpresa = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$estado, $usuarioAuditoria, $idEmpresa]);

        return $stmt->rowCount() > 0;
    }
}