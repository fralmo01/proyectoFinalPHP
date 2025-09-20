<?php
 require_once __DIR__ . '/../models/Convocatoria.php';
 
 class ConvocatoriaController
 {
     public function crear()
     {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
 
        $convModel = new Convocatoria();
         $jornadas = $convModel->listarJornadas();
         $modalidades = $convModel->listarModalidades();
 
         require_once __DIR__ . '/../../views/vistas/empresa/crear_convocatoria.php';
     }
 
     public function store()
     {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=convocatoria&action=crear');
             exit;
         }
 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

                'Creó convocatoria',
                'Convocatorias',
                 $res['idConvocatoria']
             );
 
            header('Location: index.php?controller=empresa&action=home&success=created');
             exit;
        }

        header('Location: index.php?controller=convocatoria&action=crear&error=1');
        exit;
    }

    public function editar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idConvocatoria = $_GET['id'] ?? null;
       if (!$idConvocatoria || empty($_SESSION['idEmpresa'])) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
            exit;
        }

        $convModel = new Convocatoria();
        $convocatoria = $convModel->obtenerPorId($idConvocatoria, $_SESSION['idEmpresa']);

        if (!$convocatoria) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
             exit;
         }

        $jornadas = $convModel->listarJornadas();
        $modalidades = $convModel->listarModalidades();

        require_once __DIR__ . '/../../views/vistas/empresa/modificar_empre.php';
     }
 
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=empresa&action=home');
            exit;
        }
 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $idConvocatoria = $_POST['idConvocatoria'] ?? null;
        if (!$idConvocatoria || empty($_SESSION['idEmpresa'])) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
            exit;
        }

        $convModel = new Convocatoria();
        $convocatoria = $convModel->obtenerPorId($idConvocatoria, $_SESSION['idEmpresa']);

        if (!$convocatoria) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
            exit;
        }

        $data = [
            'idConvocatoria'       => $idConvocatoria,
            'idEmpresa'            => $_SESSION['idEmpresa'],
            'titulo'               => $_POST['titulo'] ?? '',
            'descripcion'          => $_POST['descripcion'] ?? '',
            'fechaInicio'          => $_POST['fechaInicio'] ?? '',
            'fechaFin'             => $_POST['fechaFin'] ?? '',
            'idJornada'            => $_POST['idJornada'] ?? null,
            'idModalidad'          => $_POST['idModalidad'] ?? null,
            'usuarioActualizacion' => $_SESSION['idUsuario']
        ];

        $actualizado = $convModel->actualizar($data);

        if ($actualizado) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            $usuarioModel->registrarHistorial(
                $_SESSION['idUsuario'],
                'Actualizó convocatoria',
                'Convocatorias',
                $idConvocatoria
            );

            header('Location: index.php?controller=empresa&action=home&success=updated');
            exit;
        }

        header('Location: index.php?controller=convocatoria&action=editar&id=' . $idConvocatoria . '&error=update');
        exit;
    }

    public function eliminar()
    {
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=empresa&action=home');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idConvocatoria = $_POST['idConvocatoria'] ?? null;
        if (!$idConvocatoria || empty($_SESSION['idEmpresa'])) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
            exit;
        }

        $convModel = new Convocatoria();
        $convocatoria = $convModel->obtenerPorId($idConvocatoria, $_SESSION['idEmpresa']);

        if (!$convocatoria) {
            header('Location: index.php?controller=empresa&action=home&error=notfound');
            exit;
        }

        if ($convModel->eliminarLogico($idConvocatoria, $_SESSION['idEmpresa'], $_SESSION['idUsuario'])) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            $usuarioModel->registrarHistorial(
                $_SESSION['idUsuario'],
                'Desactivó convocatoria',
                'Convocatorias',
                $idConvocatoria
            );

            header('Location: index.php?controller=empresa&action=home&success=deleted');
            exit;
        }

        header('Location: index.php?controller=empresa&action=home&error=delete');
        exit;
    }

    public function listar()
    {
        $convModel = new Convocatoria();

        $modalidades = $convModel->listarModalidades();
        $jornadas = $convModel->listarJornadas();

        $idModalidad = isset($_GET['modalidad']) && $_GET['modalidad'] !== ''
            ? (int) $_GET['modalidad']
            : null;

        $idJornada = isset($_GET['jornada']) && $_GET['jornada'] !== ''
            ? (int) $_GET['jornada']
            : null;

        $filtros = [
            'idModalidad' => $idModalidad,
            'idJornada'   => $idJornada,
        ];

        $convocatorias = $convModel->listarActivas($filtros);

        require_once __DIR__ . '/../../views/vistas/listConvocatoria.php';
    }

     public function listarPorEmpresa()
     {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
 
         if (empty($_SESSION['idEmpresa'])) {
            header('Location: index.php');
             exit;
         }
 
         require_once __DIR__ . '/../models/Postulacion.php';
         $postulacionModel = new Postulacion();
         $solicitudes = $postulacionModel->listarPorEmpresa($_SESSION['idEmpresa']);
 
         require_once __DIR__ . '/../../views/vistas/empresa/lista_solicitud.php';
     }
 }
