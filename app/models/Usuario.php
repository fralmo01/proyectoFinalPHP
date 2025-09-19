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

    public function existeEmail($email)
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
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





}
