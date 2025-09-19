<?php
require_once __DIR__ . '/../models/Evaluacion.php';

class EvaluacionController
{
    public function listar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['idEmpresa'])) {
            header("Location: index.php");
            exit;
        }

        $evaluacionModel = new Evaluacion();
        $evaluaciones = $evaluacionModel->listarPorEmpresa($_SESSION['idEmpresa']);

        require_once __DIR__ . '/../../views/vistas/empresa/evaluacion.php';
    }
}