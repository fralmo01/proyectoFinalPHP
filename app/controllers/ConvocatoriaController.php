<?php
require_once __DIR__ . '/../models/Convocatoria.php';

class ConvocatoriaController
{
    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $convModel = new Convocatoria();

        $jornadas = $convModel->listarJornadas();
        $modalidades = $convModel->listarModalidades();

        require_once __DIR__ . '/../../views/vistas/empresa/crear_convocatoria.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=convocatoria&action=crear");
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
        $convModel = new Convocatoria();

        $data = [
            'titulo'          => $_POST['titulo'],
            'descripcion'     => $_POST['descripcion'],
            'fechaInicio'     => $_POST['fechaInicio'],
            'fechaFin'        => $_POST['fechaFin'],
            'idEmpresa'       => $_SESSION['idEmpresa'],
            'idJornada'       => $_POST['idJornada'],
            'idModalidad'     => $_POST['idModalidad'],
            'usuarioCreacion' => $_SESSION['idUsuario']
        ];

        $res = $convModel->crear($data);

        if ($res && isset($res['idConvocatoria']) && $res['idConvocatoria'] > 0) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            $usuarioModel->registrarHistorial(
                $_SESSION['idUsuario'],
                "Creó convocatoria",
                "Convocatorias",
                $res['idConvocatoria']
            );

            header("Location: index.php?controller=empresa&action=home&success=1");
            exit;
        } else {
            header("Location: index.php?controller=convocatoria&action=crear&error=1");
            exit;
        }
    }
}
