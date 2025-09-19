<?php
require_once __DIR__ . '/../models/Evaluacion.php';
require_once __DIR__ . '/../models/Usuario.php';

class EvaluacionController
{
    private $evaluacionModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->evaluacionModel = new Evaluacion();
        $this->usuarioModel = new Usuario();
    }

    private function asegurarSesionEmpresa(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['idEmpresa']) || empty($_SESSION['idUsuario'])) {
            header('Location: index.php');
            exit;
        }
    }

    public function listar(): void
    {
        $this->asegurarSesionEmpresa();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_etapa') {
            $this->procesarCambioEtapaDesdeListado();
            return;
        }

        $postulaciones = $this->evaluacionModel->obtenerPostulacionesPorEmpresa((int)$_SESSION['idEmpresa']);
        $categorias = $this->agruparPostulacionesPorEtapa($postulaciones);
        $mensajes = $this->obtenerMensajesDeSesion();

        require_once __DIR__ . '/../../views/vistas/empresa/evaluacion.php';
    }

    public function detalle(): void
    {
        $this->asegurarSesionEmpresa();

        $idPostulacion = isset($_GET['idPostulacion']) ? (int)$_GET['idPostulacion'] : 0;
        if ($idPostulacion <= 0 || !$this->evaluacionModel->perteneceAEmpresa($idPostulacion, (int)$_SESSION['idEmpresa'])) {
            $_SESSION['mensaje_error'] = 'La postulación seleccionada no existe o no pertenece a su empresa.';
            header('Location: index.php?controller=Evaluacion&action=listar');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            switch ($accion) {
                case 'registrar_evaluacion':
                    $this->procesarRegistroEvaluacion($idPostulacion);
                    break;
                case 'cambiar_etapa':
                    $this->procesarCambioEtapaDetalle($idPostulacion);
                    break;
                case 'guardar_resultado':
                    $this->procesarDecisionFinal($idPostulacion);
                    break;
            }
            return;
        }

        $detalle = $this->evaluacionModel->obtenerDetallePostulacion($idPostulacion);
        $evaluaciones = $this->evaluacionModel->obtenerEvaluaciones($idPostulacion);
        $historialAcciones = $this->evaluacionModel->obtenerHistorialAcciones($idPostulacion);
        $resultado = $this->evaluacionModel->obtenerResultado($idPostulacion);
        $etapas = $this->evaluacionModel->obtenerEtapas();
        $etapasPosteriores = $detalle ? $this->evaluacionModel->obtenerEtapasPosteriores((int)$detalle['idEtapa']) : [];
        $estadosResultado = $this->evaluacionModel->obtenerEstadosResultado();
        $documentosCV = $detalle ? $this->evaluacionModel->obtenerDocumentosUsuarioPorTipo((int)$detalle['idUsuario'], 'Currículum Vitae') : [];
        $mensajes = $this->obtenerMensajesDeSesion();

        require_once __DIR__ . '/../../views/vistas/empresa/Evaluaciones_Etapas.php';
    }

    private function procesarCambioEtapaDesdeListado(): void
    {
        $idPostulacion = (int)($_POST['idPostulacion'] ?? 0);
        $idEtapaActual = (int)($_POST['idEtapaActual'] ?? 0);

        if ($idPostulacion <= 0 || $idEtapaActual <= 0) {
            $_SESSION['mensaje_error'] = 'No se pudo cambiar la etapa. Datos incompletos.';
            header('Location: index.php?controller=Evaluacion&action=listar');
            exit;
        }

        if (!$this->evaluacionModel->perteneceAEmpresa($idPostulacion, (int)$_SESSION['idEmpresa'])) {
            $_SESSION['mensaje_error'] = 'No se pudo cambiar la etapa para la postulación seleccionada.';
            header('Location: index.php?controller=Evaluacion&action=listar');
            exit;
        }

        $siguienteEtapa = $this->evaluacionModel->obtenerSiguienteEtapa($idEtapaActual);
        if (!$siguienteEtapa) {
            $_SESSION['mensaje_error'] = 'La postulación ya se encuentra en la última etapa.';
            header('Location: index.php?controller=Evaluacion&action=listar');
            exit;
        }

        if ($this->evaluacionModel->actualizarEtapa($idPostulacion, (int)$siguienteEtapa['idEtapa'], (int)$_SESSION['idUsuario'])) {
            $this->usuarioModel->registrarHistorial(
                (int)$_SESSION['idUsuario'],
                'Cambio de etapa a ' . $siguienteEtapa['nombre'],
                'Postulaciones',
                $idPostulacion
            );
            $_SESSION['mensaje_exito'] = 'Etapa actualizada correctamente.';
        } else {
            $_SESSION['mensaje_error'] = 'No se realizaron cambios en la etapa.';
        }

        header('Location: index.php?controller=Evaluacion&action=listar');
        exit;
    }

    private function procesarRegistroEvaluacion(int $idPostulacion): void
    {
        $puntaje = isset($_POST['puntaje']) ? (float)$_POST['puntaje'] : 0.0;
        $observaciones = trim($_POST['observaciones'] ?? '');

        if ($puntaje < 0) {
            $_SESSION['mensaje_error'] = 'El puntaje no puede ser negativo.';
        } else {
            $idEvaluacion = $this->evaluacionModel->registrarEvaluacion($idPostulacion, $puntaje, $observaciones ?: null, (int)$_SESSION['idUsuario']);
            $this->usuarioModel->registrarHistorial(
                (int)$_SESSION['idUsuario'],
                'Registró evaluación con puntaje ' . number_format($puntaje, 2),
                'Evaluaciones',
                $idEvaluacion
            );
            $_SESSION['mensaje_exito'] = 'Evaluación registrada correctamente.';
        }

        header('Location: index.php?controller=Evaluacion&action=detalle&idPostulacion=' . $idPostulacion . '#form-evaluacion');
        exit;
    }

    private function procesarCambioEtapaDetalle(int $idPostulacion): void
    {
        $idEtapaNueva = (int)($_POST['idEtapaNueva'] ?? 0);

        if ($idEtapaNueva <= 0) {
            $_SESSION['mensaje_error'] = 'Debe seleccionar una etapa válida.';
        } else {
            if ($this->evaluacionModel->actualizarEtapa($idPostulacion, $idEtapaNueva, (int)$_SESSION['idUsuario'])) {
                $etapa = $this->evaluacionModel->obtenerEtapaPorId($idEtapaNueva);
                $nombreEtapa = $etapa['nombre'] ?? 'etapa seleccionada';
                $this->usuarioModel->registrarHistorial(
                    (int)$_SESSION['idUsuario'],
                    'Cambio de etapa manual a ' . $nombreEtapa,
                    'Postulaciones',
                    $idPostulacion
                );
                $_SESSION['mensaje_exito'] = 'La etapa ha sido actualizada.';
            } else {
                $_SESSION['mensaje_error'] = 'No se pudo actualizar la etapa seleccionada.';
            }
        }

        header('Location: index.php?controller=Evaluacion&action=detalle&idPostulacion=' . $idPostulacion . '#form-etapa');
        exit;
    }

    private function procesarDecisionFinal(int $idPostulacion): void
    {
        $idEstadoResultado = (int)($_POST['idEstadoResultado'] ?? 0);

        if ($idEstadoResultado <= 0) {
            $_SESSION['mensaje_error'] = 'Debe seleccionar un resultado válido.';
        } else {
            $idResultado = $this->evaluacionModel->guardarResultado($idPostulacion, $idEstadoResultado, (int)$_SESSION['idUsuario']);
            $estados = $this->evaluacionModel->obtenerEstadosResultado();
            $estadoSeleccionado = null;
            foreach ($estados as $estado) {
                if ((int)$estado['idEstadoResultado'] === $idEstadoResultado) {
                    $estadoSeleccionado = $estado['nombre'];
                    break;
                }
            }
            $this->usuarioModel->registrarHistorial(
                (int)$_SESSION['idUsuario'],
                'Registró decisión final: ' . ($estadoSeleccionado ?? 'Estado seleccionado'),
                'Resultados',
                $idResultado
            );
            $_SESSION['mensaje_exito'] = 'La decisión final ha sido guardada.';
        }

        header('Location: index.php?controller=Evaluacion&action=detalle&idPostulacion=' . $idPostulacion . '#form-resultado');
        exit;
    }

    private function agruparPostulacionesPorEtapa(array $postulaciones): array
    {
        $categorias = [
            'pendientes' => [],
            'revision' => [],
            'entrevistas' => [],
            'pruebas' => [],
        ];

        foreach ($postulaciones as $postulacion) {
            $nombreEtapa = $postulacion['etapa'] ?? '';
            $nombreEtapaNormalizado = $this->normalizarTexto($nombreEtapa);

            if ($nombreEtapaNormalizado === 'revisión inicial') {
                $categorias['revision'][] = $postulacion;
            } elseif (in_array($nombreEtapaNormalizado, ['entrevista telefónica', 'entrevista presencial'], true)) {
                $categorias['entrevistas'][] = $postulacion;
            } elseif (in_array($nombreEtapaNormalizado, ['prueba técnica', 'selección final'], true)) {
                $categorias['pruebas'][] = $postulacion;
            } else {
                $categorias['pendientes'][] = $postulacion;
            }
        }

        return $categorias;
    }

    private function normalizarTexto(?string $texto): string
    {
        if (!$texto) {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($texto, 'UTF-8');
        }

        return strtolower($texto);
    }

    private function obtenerMensajesDeSesion(): array
    {
        $mensajes = [
            'exito' => $_SESSION['mensaje_exito'] ?? null,
            'error' => $_SESSION['mensaje_error'] ?? null,
        ];
        unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
        return $mensajes;
    }
}


