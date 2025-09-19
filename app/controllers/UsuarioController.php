<?php
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{

    public function login()
    {
        require_once __DIR__ . '/../../views/vistas/login_user.php';
    }

    public function create()
    {
        $tipo = $_GET['tipo'] ?? 'postulante';
        require_once __DIR__ . '/../../views/vistas/create_user.php';
    }

    public function store()
    {
        $usuarioModel = new Usuario();
        $errores = [];
        $success = "";

        if (empty($_POST['nombre']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['email']) || empty($_POST['rol'])) {
            $errores[] = "Debes completar todos los campos obligatorios.";
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido.";
        }

        if ($_POST['rol'] == "empresa" && !preg_match('/^[0-9]{9}$/', $_POST['telefono'] ?? '')) {
            $errores[] = "El teléfono de empresa debe tener exactamente 9 dígitos.";
        }

        if ($usuarioModel->existeUsuario($_POST['usuario'])) {
            $errores[] = "El nombre de usuario ya está en uso.";
        }

        if ($usuarioModel->existeEmail($_POST['email'])) {
            $errores[] = "El correo electrónico ya está en uso.";
        }

        if (!empty($errores)) {
            $tipo = $_POST['rol'] ?? 'postulante';
            $mensajes = $errores;
            require "../views/vistas/create_user.php";
            return;
        }

        $data = [
            'nombre' => $_POST['nombre'],
            'apellidoPaterno' => $_POST['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $_POST['apellidoMaterno'] ?? '',
            'usuario' => $_POST['usuario'],
            'clave' => $_POST['clave'],
            'email' => $_POST['email'],
            'idRol' => ($_POST['rol'] == "empresa") ? 2 : 3
        ];

        if ($_POST['rol'] == "empresa") {
            $data['razonSocial'] = $_POST['razonSocial'];
            $data['direccion'] = $_POST['direccion'] ?? '';
            $data['telefono'] = $_POST['telefono'] ?? '';
            $result = $usuarioModel->crearEmpresa($data);

            $idUsuario = $result['idUsuario'];
            $_SESSION['idEmpresa'] = $result['idEmpresa'];
        } else {
            $idUsuario = $usuarioModel->crearPostulante($data);
        }

        if ($idUsuario > 0) {
            $usuarioModel->registrarHistorial($idUsuario, 'Creación de cuenta', 'Usuarios', $idUsuario);
            $success = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
            $tipo = $_POST['rol'];
            require "../views/vistas/create_user.php";
        } else {
            $errores[] = "Error al crear el usuario.";
            $tipo = $_POST['rol'] ?? 'postulante';
            $mensajes = $errores;
            require "../views/vistas/create_user.php";
        }
    }

    public function auth()
    {
        $usuarioModel = new Usuario();
        $usuario = $_POST['usuario'] ?? '';
        $clave = $_POST['clave'] ?? '';

        $result = $usuarioModel->login($usuario, $clave);

        if ($result) {
            session_start();
            $_SESSION['idUsuario'] = $result['idUsuario'];
            $_SESSION['usuario'] = $result['usuario'];
            $_SESSION['nombre'] = $result['nombre'];
            $_SESSION['apellidoPaterno'] = $result['apellidoPaterno'] ?? '';
            $_SESSION['apellidoMaterno'] = $result['apellidoMaterno'] ?? '';
            $_SESSION['email'] = $result['email'];
            $_SESSION['telefono'] = $result['telefono'] ?? '';
            $_SESSION['direccion'] = $result['direccion'] ?? '';
            $_SESSION['nacionalidad'] = $result['nacionalidad'] ?? '';
            $_SESSION['rol'] = $result['rol'];
            $_SESSION['idEmpresa'] = $result['idEmpresa'] ?? null;
            $_SESSION['fotoPerfil'] = $result['fotoPerfil'] ?? 'default.png';

            $usuarioModel->registrarHistorial(
                $result['idUsuario'],
                'Inicio de sesión',
                'Usuarios',
                $result['idUsuario']
            );

            if ($result['rol'] == 'Administrador') {
                header("Location: index.php?controller=admin&action=dashboard");
            } elseif ($result['rol'] == 'Empresa') {
                header("Location: index.php?controller=empresa&action=home");
            } else {
                header("Location: index.php?controller=postulante&action=home");
            }
            exit;
        } else {
            $mensajeError = "Usuario o contraseña incorrectos.";
            require "../views/vistas/login_user.php";
        }
    }


    public function updatePerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=usuario&action=perfil");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $usuarioModel = new Usuario();

        $fotoPerfil = $_SESSION['fotoPerfil'] ?? 'default.png';
        if (!empty($_FILES['fotoPerfil']['name'])) {
            $nombreArchivo = uniqid() . "_" . basename($_FILES['fotoPerfil']['name']);
            $directorio = __DIR__ . "/../../public/fotos/";

            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $rutaDestino = $directorio . $nombreArchivo;

            if (move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $rutaDestino)) {
                $fotoAnterior = $directorio . $fotoPerfil;
                if ($fotoPerfil !== 'default.png' && file_exists($fotoAnterior)) {
                    unlink($fotoAnterior);
                }
                $fotoPerfil = $nombreArchivo;
            }
        }

        $data = [
            'idUsuario' => $_SESSION['idUsuario'],
            'nombre' => $_POST['nombre'] ?? '',
            'apellidoPaterno' => $_POST['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $_POST['apellidoMaterno'] ?? '',
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'fotoPerfil' => $fotoPerfil,
            'nacionalidad' => $_POST['nacionalidad'] ?? '',
            'idSexo' => $_POST['idSexo'] ?? null
        ];

        if ($usuarioModel->actualizarUsuario($data)) {
            $_SESSION = array_merge($_SESSION, $data);

            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], "Actualizó su perfil", "Usuarios", $_SESSION['idUsuario']);

            if ($_SESSION['rol'] === 'Empresa') {
                header("Location: index.php?controller=usuario&action=perfil&success=1");
            } elseif ($_SESSION['rol'] === 'Postulante') {
                header("Location: index.php?controller=postulante&action=perfil&success=1");
            } else {
                header("Location: index.php?controller=usuario&action=perfil&success=1");
            }
            exit;
        } else {
            if ($_SESSION['rol'] === 'Empresa') {
                header("Location: index.php?controller=usuario&action=perfil&error=1");
            } elseif ($_SESSION['rol'] === 'Postulante') {
                header("Location: index.php?controller=postulante&action=perfil&error=1");
            } else {
                header("Location: index.php?controller=usuario&action=perfil&error=1");
            }
            exit;
        }
    }

    public function perfil()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        require_once __DIR__ . '/../../views/vistas/empresa/perfil_user.php';
    }

    public function actualizarCV()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        require_once __DIR__ . '/../models/Documento.php';
        $docModel = new Documento();

        $tipos = $docModel->listarTipos();
        $documentos = $docModel->listarPorUsuario($_SESSION['idUsuario']);

        require_once __DIR__ . '/../../views/vistas/usuario/actualizar_cv.php';
    }

    public function guardarCV()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=usuario&action=actualizarCV");
            exit;
        }
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        require_once __DIR__ . '/../models/Documento.php';
        $docModel = new Documento();

        $idUsuario = $_SESSION['idUsuario'];
        $idTipoDocumento = intval($_POST['idTipoDocumento'] ?? 0);

        if ($idTipoDocumento <= 0) {
            header("Location: index.php?controller=usuario&action=actualizarCV&error=tipo");
            exit;
        }
        if (empty($_FILES['documento']['name'])) {
            header("Location: index.php?controller=usuario&action=actualizarCV&error=archivo");
            exit;
        }

        $dir = __DIR__ . "/../../public/documentos/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
        $nombreArchivo = uniqid('doc_') . "." . $ext;
        $rutaDestinoFs = $dir . $nombreArchivo;
        $rutaRelativa = "documentos/" . $nombreArchivo;

        if (!move_uploaded_file($_FILES['documento']['tmp_name'], $rutaDestinoFs)) {
            header("Location: index.php?controller=usuario&action=actualizarCV&error=subida");
            exit;
        }

        $res = $docModel->upsertDocumento($idUsuario, $idTipoDocumento, $rutaRelativa, $idUsuario);

        if ($res && !empty($res['rutaAnterior'])) {
            $oldFs = __DIR__ . "/../../public/" . $res['rutaAnterior'];
            if (is_file($oldFs)) {
                unlink($oldFs);
            }
        }

        header("Location: index.php?controller=usuario&action=actualizarCV&success=1");
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['idUsuario'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], "Cerró sesión", "Usuarios", $_SESSION['idUsuario']);
        }

        $_SESSION = [];
        session_unset();
        session_destroy();

        header("Location: index.php?controller=usuario&action=login&logout=1");
        exit;
    }
}
