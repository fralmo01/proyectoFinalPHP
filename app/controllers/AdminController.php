<?php
class AdminController
{
    public function dashboard()
    {
        require_once __DIR__ . '/../../views/vistas/administrador/home_admin.php';
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
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: '¡Logo actualizado!',
            text: 'Los datos de tu empresa se han guardado correctamente.',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = 'index.php?controller=empresa&action=editar';
        });
    </script>";
    }






}
