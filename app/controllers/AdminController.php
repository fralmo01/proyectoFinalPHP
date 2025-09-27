<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/Empresa.php';

class AdminController
{
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function dashboard()
    {
        require_once __DIR__ . '/../../views/vistas/administrador/home_admin.php';
    }

    public function usuarios()
    {
        $this->ensureSession();

        $usuarioModel = new Usuario();
        $rolModel = new Rol();

        $rolFiltro = $_GET['rol'] ?? 'todos';
        $estadoFiltro = $_GET['estado'] ?? 'activos';

        $usuarios = $usuarioModel->listarUsuarios($rolFiltro, $estadoFiltro);
        $roles = $rolModel->getAllActives();
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../../views/vistas/administrador/usuarios.php';
    }

    public function nuevoUsuario()
    {
        $this->ensureSession();

        $rolModel = new Rol();
        $empresaModel = new Empresa();

        $roles = $rolModel->getAllActives();
        $empresas = $empresaModel->listarActivas();
        $usuario = ['estado' => 1];
        $errores = [];

        require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
    }

    public function guardarUsuario()
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=usuarios');
            exit;
        }

        $usuarioModel = new Usuario();
        $rolModel = new Rol();
        $empresaModel = new Empresa();

        $roles = $rolModel->getAllActives();
        $empresas = $empresaModel->listarActivas();

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellidoPaterno' => trim($_POST['apellidoPaterno'] ?? ''),
            'apellidoMaterno' => trim($_POST['apellidoMaterno'] ?? ''),
            'usuario' => trim($_POST['usuario'] ?? ''),
            'clave' => $_POST['clave'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'idRol' => (int) ($_POST['idRol'] ?? 0),
            'estado' => isset($_POST['estado']) && (int) $_POST['estado'] === 0 ? 0 : 1,
            'idEmpresa' => null,
            'usuarioAuditoria' => $_SESSION['idUsuario'] ?? null
        ];

        if ($data['idRol'] === 2) {
            $empresaSeleccionada = $_POST['idEmpresa'] ?? '';
            $data['idEmpresa'] = $empresaSeleccionada !== '' ? (int) $empresaSeleccionada : null;
        }

        $errores = $this->validarUsuarioAdmin($data, true);

        if ($data['idRol'] === 2 && empty($data['idEmpresa'])) {
            $errores[] = 'Debes seleccionar una empresa para el usuario con rol Empresa.';
        }

        if (!empty($data['usuario']) && $usuarioModel->existeUsuario($data['usuario'])) {
            $errores[] = 'El nombre de usuario ya se encuentra registrado.';
        }

        if (!empty($data['email']) && $usuarioModel->existeEmail($data['email'])) {
            $errores[] = 'El correo electrónico ya se encuentra registrado.';
        }

        if (!empty($errores)) {
            $usuario = $data;
            require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
            return;
        }

        $idNuevo = $usuarioModel->crearDesdeAdmin($data);

        if ($idNuevo > 0) {
            if (!empty($data['usuarioAuditoria'])) {
                $usuarioModel->registrarHistorial($data['usuarioAuditoria'], 'Creación de usuario desde panel', 'Usuarios', $idNuevo);
            }

            header('Location: index.php?controller=admin&action=usuarios&mensaje=Usuario+creado+correctamente');
            exit;
        }

        $errores[] = 'No se pudo crear el usuario. Intenta nuevamente.';
        $usuario = $data;
        require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
    }

    public function editarUsuario()
    {
        $this->ensureSession();

        $idUsuario = (int) ($_GET['id'] ?? 0);
        if ($idUsuario <= 0) {
            header('Location: index.php?controller=admin&action=usuarios&error=Usuario+no+encontrado');
            exit;
        }

        $usuarioModel = new Usuario();
        $rolModel = new Rol();
        $empresaModel = new Empresa();

        $usuario = $usuarioModel->obtenerPorId($idUsuario);
        if (!$usuario) {
            header('Location: index.php?controller=admin&action=usuarios&error=Usuario+no+encontrado');
            exit;
        }

        $roles = $rolModel->getAllActives();
        $empresas = $empresaModel->listarActivas();

        if (!empty($usuario['idEmpresa'])) {
            $existeEnLista = array_filter($empresas, function ($empresa) use ($usuario) {
                return (int) $empresa['idEmpresa'] === (int) $usuario['idEmpresa'];
            });

            if (empty($existeEnLista)) {
                $empresas[] = [
                    'idEmpresa' => $usuario['idEmpresa'],
                    'nombre' => $usuario['empresaNombre'] ?? 'Empresa asociada'
                ];
            }
        }

        $errores = [];

        require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
    }

    public function actualizarUsuario()
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=usuarios');
            exit;
        }

        $usuarioModel = new Usuario();
        $rolModel = new Rol();
        $empresaModel = new Empresa();

        $roles = $rolModel->getAllActives();
        $empresas = $empresaModel->listarActivas();

        $data = [
            'idUsuario' => (int) ($_POST['idUsuario'] ?? 0),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellidoPaterno' => trim($_POST['apellidoPaterno'] ?? ''),
            'apellidoMaterno' => trim($_POST['apellidoMaterno'] ?? ''),
            'usuario' => trim($_POST['usuario'] ?? ''),
            'clave' => $_POST['clave'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'idRol' => (int) ($_POST['idRol'] ?? 0),
            'estado' => isset($_POST['estado']) && (int) $_POST['estado'] === 0 ? 0 : 1,
            'idEmpresa' => null,
            'usuarioAuditoria' => $_SESSION['idUsuario'] ?? null
        ];

        if ($data['idUsuario'] <= 0) {
            header('Location: index.php?controller=admin&action=usuarios&error=Usuario+no+válido');
            exit;
        }

        if ($data['idRol'] === 2) {
            $empresaSeleccionada = $_POST['idEmpresa'] ?? '';
            $data['idEmpresa'] = $empresaSeleccionada !== '' ? (int) $empresaSeleccionada : null;
        }

        $errores = $this->validarUsuarioAdmin($data, false);

        if ($data['idRol'] === 2 && empty($data['idEmpresa'])) {
            $errores[] = 'Debes seleccionar una empresa para el usuario con rol Empresa.';
        }

        if (!empty($data['usuario']) && $usuarioModel->existeUsuarioExcepto($data['usuario'], $data['idUsuario'])) {
            $errores[] = 'El nombre de usuario ya se encuentra registrado.';
        }

        if (!empty($data['email']) && $usuarioModel->existeEmailExcepto($data['email'], $data['idUsuario'])) {
            $errores[] = 'El correo electrónico ya se encuentra registrado.';
        }

        if (!empty($errores)) {
            $usuario = $data;
            require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
            return;
        }

        $exito = $usuarioModel->actualizarDesdeAdmin($data);

        if ($exito) {
            if (!empty($data['usuarioAuditoria'])) {
                $usuarioModel->registrarHistorial($data['usuarioAuditoria'], 'Actualización de usuario desde panel', 'Usuarios', $data['idUsuario']);
            }

            header('Location: index.php?controller=admin&action=usuarios&mensaje=Usuario+actualizado+correctamente');
            exit;
        }

        $errores[] = 'No se pudo actualizar el usuario. Intenta nuevamente.';
        $usuario = $data;
        require_once __DIR__ . '/../../views/vistas/administrador/form_usuario.php';
    }

    public function cambiarEstadoUsuario()
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=usuarios');
            exit;
        }

        $idUsuario = (int) ($_POST['idUsuario'] ?? 0);
        $nuevoEstado = isset($_POST['nuevoEstado']) && (int) $_POST['nuevoEstado'] === 0 ? 0 : 1;

        if ($idUsuario <= 0) {
            header('Location: index.php?controller=admin&action=usuarios&error=Usuario+no+válido');
            exit;
        }

        if (!empty($_SESSION['idUsuario']) && $idUsuario === (int) $_SESSION['idUsuario'] && $nuevoEstado === 0) {
            header('Location: index.php?controller=admin&action=usuarios&error=No+puedes+desactivar+tu+propio+usuario');
            exit;
        }

        $usuarioModel = new Usuario();
        $exito = $usuarioModel->actualizarEstado($idUsuario, $nuevoEstado, $_SESSION['idUsuario'] ?? null);

        if ($exito && !empty($_SESSION['idUsuario'])) {
            $accion = $nuevoEstado === 1 ? 'Activó' : 'Desactivó';
            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], "$accion usuario desde panel", 'Usuarios', $idUsuario);
        }

        if ($exito) {
            $mensaje = $nuevoEstado === 1 ? 'Usuario+activado+correctamente' : 'Usuario+desactivado+correctamente';
            header('Location: index.php?controller=admin&action=usuarios&mensaje=' . $mensaje);
            exit;
        }

        header('Location: index.php?controller=admin&action=usuarios&error=No+se+pudo+actualizar+el+estado+del+usuario');
    }

    public function update()
    {
        $this->ensureSession();

        $empresaModel = new Empresa();

        $logoEmpresa = $_SESSION['logoEmpresa'] ?? null;
        if (!empty($_FILES['logoEmpresa']['name'])) {
            $nombreArchivo = uniqid() . "_" . basename($_FILES['logoEmpresa']['name']);
            $rutaDestino = __DIR__ . "/../../public/fotos/empresalogo/" . $nombreArchivo;

            if (move_uploaded_file($_FILES['logoEmpresa']['tmp_name'], $rutaDestino)) {
                $logoEmpresa = $nombreArchivo;
            }
        }

        $data = [
            'idEmpresa' => $_SESSION['idEmpresa'],
            'nombre' => $_POST['nombre'],
            'direccion' => $_POST['direccion'],
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'],
            'sitioWeb' => $_POST['sitioWeb'],
            'logoEmpresa' => $logoEmpresa
        ];

        $empresaModel->actualizarEmpresa($data);

        $_SESSION['razonSocial'] = $data['nombre'];
        $_SESSION['empresaDireccion'] = $data['direccion'];
        $_SESSION['empresaTelefono'] = $data['telefono'];
        $_SESSION['empresaEmail'] = $data['email'];
        $_SESSION['empresaWeb'] = $data['sitioWeb'];
        $_SESSION['logoEmpresa'] = $data['logoEmpresa'];

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>",
            "        Swal.fire({",
            "            icon: 'success',",
            "            title: '¡Logo actualizado!',",
            "            text: 'Los datos de tu empresa se han guardado correctamente.',",
            "            confirmButtonText: 'Aceptar'",
            "        }).then(() => {",
            "            window.location.href = 'index.php?controller=empresa&action=editar';",
            "        });",
            "    </script>";
    }

    private function validarUsuarioAdmin(array $data, bool $esNuevo): array
    {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores[] = 'El nombre es obligatorio.';
        }

        if (empty($data['usuario'])) {
            $errores[] = 'El nombre de usuario es obligatorio.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Debes ingresar un correo electrónico válido.';
        }

        if (empty($data['idRol'])) {
            $errores[] = 'Debes seleccionar un rol válido.';
        }

        if ($esNuevo && empty($data['clave'])) {
            $errores[] = 'Debes asignar una contraseña al nuevo usuario.';
        }

        if (!empty($data['telefono']) && !preg_match('/^[0-9+()\s-]{6,20}$/', $data['telefono'])) {
            $errores[] = 'El formato del teléfono no es válido.';
        }

        return $errores;
    }
}