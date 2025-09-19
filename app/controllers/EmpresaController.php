<?php
require_once __DIR__ . '/../models/Empresa.php';

class EmpresaController
{

    public function home()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        require_once __DIR__ . '/../models/Convocatoria.php';
        $convModel = new Convocatoria();

        $convocatorias = $convModel->listarPorEmpresa($_SESSION['idEmpresa']);
        require_once __DIR__ . '/../../views/vistas/empresa/home_empresa.php';
    }


    public function editar()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $empresaModel = new Empresa();

        $empresa = $empresaModel->getEmpresaById($_SESSION['idEmpresa']);

        $_SESSION['razonSocial'] = $empresa['nombre'] ?? '';
        $_SESSION['empresaDireccion'] = $empresa['direccion'] ?? '';
        $_SESSION['empresaTelefono'] = $empresa['telefono'] ?? '';
        $_SESSION['empresaEmail'] = $empresa['email'] ?? '';
        $_SESSION['empresaWeb'] = $empresa['sitioWeb'] ?? '';
        $_SESSION['logoEmpresa'] = $empresa['logoEmpresa'] ?? 'default_logo.png';

        require_once __DIR__ . '/../../views/vistas/empresa/perfil_empresa.php';
    }


    public function update()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $empresaModel = new Empresa();

        $logoEmpresa = $_SESSION['logoEmpresa'] ?? null;
        if (!empty($_FILES['logoEmpresa']['name'])) {
            $nombreArchivo = uniqid() . "_" . basename($_FILES['logoEmpresa']['name']);
            $rutaDestino = __DIR__ . "/../../public/fotos/empresalogo/" . $nombreArchivo;

            if (move_uploaded_file($_FILES['logoEmpresa']['tmp_name'], $rutaDestino)) {
                if (!empty($logoEmpresa)) {
                    $logoAnterior = __DIR__ . "/../../public/fotos/empresalogo/" . $logoEmpresa;
                    if (file_exists($logoAnterior))
                        unlink($logoAnterior);
                }
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

        if ($empresaModel->actualizarEmpresa($data)) {
            $_SESSION['razonSocial'] = $data['nombre'];
            $_SESSION['empresaDireccion'] = $data['direccion'];
            $_SESSION['empresaTelefono'] = $data['telefono'];
            $_SESSION['empresaEmail'] = $data['email'];
            $_SESSION['empresaWeb'] = $data['sitioWeb'];
            $_SESSION['logoEmpresa'] = $data['logoEmpresa'];

            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], "Actualizó datos de su empresa", "Empresas", $_SESSION['idEmpresa']);

            header("Location: index.php?controller=empresa&action=editar&success=1");
            exit;
        } else {
            header("Location: index.php?controller=empresa&action=editar&error=1");
            exit;
        }
    }

}
