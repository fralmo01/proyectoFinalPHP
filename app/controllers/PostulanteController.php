<?php
require_once __DIR__ . '/../models/Convocatoria.php';
require_once __DIR__ . '/../models/Documento.php';
require_once __DIR__ . '/../models/Postulacion.php';
require_once __DIR__ . '/../models/Usuario.php';

class PostulanteController
{

    public function home()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
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

        $detalle = null;
        if (!empty($_GET['idConvocatoria'])) {
            $idConvocatoria = (int) $_GET['idConvocatoria'];
            foreach ($convocatorias as $c) {
                if ((int) $c['idConvocatoria'] === $idConvocatoria) {
                    $detalle = $c;
                    break;
                }
            }
        } elseif (!empty($convocatorias)) {
            $detalle = $convocatorias[0];
        }

        require_once __DIR__ . '/../../views/vistas/postulante/home_postulante.php';
    }

    public function perfil()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require_once __DIR__ . '/../../views/vistas/postulante/perfil_postulante.php';
    }

    public function postular()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=postulante&action=home");
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();

        $idConvocatoria = intval($_POST['idConvocatoria'] ?? 0);
        $comentario = $_POST['comentario'] ?? '';

        if ($idConvocatoria <= 0) {
            header("Location: index.php?controller=postulante&action=home&error=convocatoria");
            exit;
        }

        $postModel = new Postulacion();
        $res = $postModel->postular($_SESSION['idUsuario'], $idConvocatoria, $comentario);

        $usuarioModel = new Usuario();

        if ($res && $res['idPostulacion'] > 0) {
            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], "Postulación a convocatoria", "Postulaciones", $res['idPostulacion']);

            header("Location: index.php?controller=postulante&action=misPostulaciones&success=1");
        } else {
            header("Location: index.php?controller=postulante&action=home&error=postulacion");
        }
        exit;
    }

    public function misPostulaciones()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $postModel = new Postulacion();
        $postulaciones = $postModel->listarPorUsuario($_SESSION['idUsuario']);

        require_once __DIR__ . '/../../views/vistas/postulante/mispostulaciones.php';
    }

    public function documentos()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $docModel = new Documento();

        $tipos = $docModel->listarTipos();
        $documentos = $docModel->listarPorUsuario($_SESSION['idUsuario']);

        require_once __DIR__ . '/../../views/vistas/postulante/actualizar_cv.php';
    }

    public function guardarDocumento()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=postulante&action=documentos");
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();

        $docModel = new Documento();
        $idUsuario = $_SESSION['idUsuario'];
        $idTipoDocumento = intval($_POST['idTipoDocumento'] ?? 0);

        if ($idTipoDocumento <= 0 || empty($_FILES['documento']['name'])) {
            header("Location: index.php?controller=postulante&action=documentos&error=1");
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
            header("Location: index.php?controller=postulante&action=documentos&error=subida");
            exit;
        }

        $res = $docModel->upsertDocumento($idUsuario, $idTipoDocumento, $rutaRelativa, $idUsuario);

        if ($res && !empty($res['rutaAnterior'])) {
            $oldFs = __DIR__ . "/../../public/" . $res['rutaAnterior'];
            if (is_file($oldFs)) unlink($oldFs);
        }

        header("Location: index.php?controller=postulante&action=documentos&success=1");
        exit;
    }
}

