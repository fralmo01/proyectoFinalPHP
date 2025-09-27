<?php
require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function existeUsuario($usuario)
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    public function existeUsuarioExcepto($usuario, $idUsuario)
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE usuario = ? AND idUsuario <> ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario, $idUsuario]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    public function existeEmail($email)
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    public function existeEmailExcepto($email, $idUsuario)
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE email = ? AND idUsuario <> ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email, $idUsuario]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    public function crearPostulante($data)
    {
        $sql = "CALL sp_crear_postulante(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellidoPaterno'],
            $data['apellidoMaterno'],
            $data['usuario'],
            $data['clave'],
            $data['email'],
            $data['idRol']
        ]);
        $result = $stmt->fetch();
        return $result['idUsuario'] ?? 0;
    }

    public function crearEmpresa($data): array
    {
        $sql = "CALL sp_crear_empresa(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellidoPaterno'],
            $data['apellidoMaterno'],
            $data['usuario'],
            $data['clave'],
            $data['email'],
            $data['idRol'],
            $data['razonSocial'],
            $data['direccion'],
            $data['telefono']
        ]);
        $result = $stmt->fetch();
        return $result ?: ['idUsuario' => 0, 'idEmpresa' => null];
    }

    public function crearDesdeAdmin(array $data): int
    {
        $sql = "INSERT INTO Usuarios (nombre, apellidoPaterno, apellidoMaterno, usuario, clave, email, telefono, direccion, idRol, idEmpresa, estado, usuarioCreacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['apellidoPaterno'],
            $data['apellidoMaterno'],
            $data['usuario'],
            hash('sha256', $data['clave']),
            $data['email'],
            $data['telefono'],
            $data['direccion'],
            $data['idRol'],
            $data['idEmpresa'],
            $data['estado'],
            $data['usuarioAuditoria']
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function registrarHistorial($idUsuario, $accion, $tabla, $idRegistro)
    {
        $sql = "CALL sp_registrar_historial(?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario, $accion, $tabla, $idRegistro]);
    }

    public function login($usuario, $clave)
    {
        $sql = "CALL sp_login_usuario(?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario, $clave]);
        return $stmt->fetch();
    }

    public function obtenerPorId(int $idUsuario): ?array
    {
        $sql = "SELECT u.*, r.nombre AS rolNombre, e.nombre AS empresaNombre
                FROM Usuarios u
                INNER JOIN Roles r ON u.idRol = r.idRol
                LEFT JOIN Empresas e ON u.idEmpresa = e.idEmpresa
                WHERE u.idUsuario = ?
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public function listarUsuarios(string $rolFiltro = 'todos', string $estadoFiltro = 'activos'): array
    {
        $sql = "SELECT u.idUsuario, u.nombre, u.apellidoPaterno, u.apellidoMaterno, u.usuario, u.email, u.telefono,
                       u.estado, u.fechaRegistro, r.nombre AS rolNombre, e.nombre AS empresaNombre
                FROM Usuarios u
                INNER JOIN Roles r ON u.idRol = r.idRol
                LEFT JOIN Empresas e ON u.idEmpresa = e.idEmpresa
                WHERE 1 = 1";
        $params = [];

        if ($rolFiltro !== 'todos') {
            $mapaRoles = [
                'administrador' => 'Administrador',
                'empresa' => 'Empresa',
                'postulante' => 'Postulante'
            ];

            if (isset($mapaRoles[$rolFiltro])) {
                $sql .= " AND r.nombre = ?";
                $params[] = $mapaRoles[$rolFiltro];
            }
        }

        if ($estadoFiltro === 'activos') {
            $sql .= " AND u.estado = 1";
        } elseif ($estadoFiltro === 'inactivos') {
            $sql .= " AND u.estado = 0";
        }

        $sql .= " ORDER BY u.fechaRegistro DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($data)
    {
        $sql = "CALL sp_actualizar_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['idUsuario'],
            $data['nombre'],
            $data['apellidoPaterno'],
            $data['apellidoMaterno'],
            $data['email'],
            $data['telefono'],
            $data['direccion'],
            $data['fotoPerfil'],
            $data['nacionalidad'],
            $data['idSexo']
        ]);
        return $stmt->fetch();
    }

    public function actualizarDesdeAdmin(array $data): bool
    {
        $campos = [
            'nombre = ?',
            'apellidoPaterno = ?',
            'apellidoMaterno = ?',
            'usuario = ?',
            'email = ?',
            'telefono = ?',
            'direccion = ?',
            'idRol = ?',
            'idEmpresa = ?',
            'estado = ?',
            'usuarioActualizacion = ?',
            'fechaActualizacion = NOW()'
        ];

        $params = [
            $data['nombre'],
            $data['apellidoPaterno'],
            $data['apellidoMaterno'],
            $data['usuario'],
            $data['email'],
            $data['telefono'],
            $data['direccion'],
            $data['idRol'],
            $data['idEmpresa'],
            $data['estado'],
            $data['usuarioAuditoria']
        ];

        if (!empty($data['clave'])) {
            $campos[] = 'clave = ?';
            $params[] = hash('sha256', $data['clave']);
        }

        $params[] = $data['idUsuario'];

        $sql = 'UPDATE Usuarios SET ' . implode(', ', $campos) . ' WHERE idUsuario = ?';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function actualizarEstado(int $idUsuario, int $estado, ?int $usuarioAuditoria = null): bool
    {
        $sql = "UPDATE Usuarios
                SET estado = ?, usuarioActualizacion = ?, fechaActualizacion = NOW()
                WHERE idUsuario = ?";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$estado, $usuarioAuditoria, $idUsuario]);
    }
}