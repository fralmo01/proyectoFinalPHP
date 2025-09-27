<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Convocatoria.php';
require_once __DIR__ . '/../models/Historial.php';

class AdminController
{
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function sanitizeFecha(?string $fecha): ?string
    {
        if (empty($fecha)) {
            return null;
        }

        $fecha = trim($fecha);
        $dateTime = \DateTime::createFromFormat('Y-m-d', $fecha);

        return ($dateTime instanceof \DateTime && $dateTime->format('Y-m-d') === $fecha)
            ? $fecha
            : null;
    }

    public function dashboard()
    {
        require_once __DIR__ . '/../../views/vistas/administrador/home_admin.php';
    }

    public function reportes(): void
    {
        $this->ensureSession();

        $historialModel = new Historial();

        $fechaInicio = $this->sanitizeFecha($_GET['fechaInicio'] ?? null);
        $fechaFin = $this->sanitizeFecha($_GET['fechaFin'] ?? null);

        if ($fechaInicio !== null && $fechaFin !== null) {
            $inicio = new \DateTime($fechaInicio);
            $fin = new \DateTime($fechaFin);

            if ($inicio > $fin) {
                $fechaFin = $fechaInicio;
            }
        }

        $historial = $historialModel->obtenerHistorial($fechaInicio, $fechaFin);
        $accionesPorUsuario = $historialModel->obtenerAccionesPorUsuario($fechaInicio, $fechaFin);

        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../../views/vistas/administrador/reportes_historial.php';
    }

    public function exportarHistorialExcel(): void
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=reportes');
            exit;
        }

        $fechaInicio = $this->sanitizeFecha($_POST['fechaInicio'] ?? null);
        $fechaFin = $this->sanitizeFecha($_POST['fechaFin'] ?? null);

        if ($fechaInicio !== null && $fechaFin !== null) {
            $inicio = new \DateTime($fechaInicio);
            $fin = new \DateTime($fechaFin);

            if ($inicio > $fin) {
                $fechaFin = $fechaInicio;
            }
        }

        $historialModel = new Historial();
        $historial = $historialModel->obtenerHistorial($fechaInicio, $fechaFin);

        $bootstrapPath = __DIR__ . '/../libreria/PhpSpreadsheet-5.1.0/src/Bootstrap.php';
        if (!file_exists($bootstrapPath)) {
            header('Location: index.php?controller=admin&action=reportes&error=No+se+encontr%C3%B3+PhpSpreadsheet');
            exit;
        }

        require_once $bootstrapPath;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Gestión de Reclutamiento')
            ->setTitle('Historial de acciones');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Historial');

        $encabezados = ['ID acción', 'Usuario', 'Acción', 'Tabla afectada', 'ID registro', 'Fecha'];
        $sheet->fromArray($encabezados, null, 'A1');

        $fila = 2;
        foreach ($historial as $registro) {
            $sheet->setCellValue('A' . $fila, $registro['idAccion']);
            $usuario = trim($registro['nombreUsuario'] ?? '');
            $sheet->setCellValue('B' . $fila, $usuario !== '' ? $usuario : 'Sin usuario');
            $sheet->setCellValue('C' . $fila, $registro['accion']);
            $sheet->setCellValue('D' . $fila, $registro['tablaAfectada']);
            $sheet->setCellValue('E' . $fila, $registro['idRegistro']);
            $sheet->setCellValue('F' . $fila, $registro['fechaAccion']);
            $fila++;
        }

        foreach (range('A', 'F') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        if ($fechaInicio !== null || $fechaFin !== null) {
            $sheet->setCellValue('A' . $fila, 'Rango aplicado:');
            $sheet->setCellValue('B' . $fila, ($fechaInicio ?? 'Sin inicio') . ' al ' . ($fechaFin ?? 'Sin fin'));
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="historial_acciones.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function descargarGraficoPdf(): void
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=reportes');
            exit;
        }

        $grafico = $_POST['grafico'] ?? '';
        $fechaInicio = $this->sanitizeFecha($_POST['fechaInicio'] ?? null);
        $fechaFin = $this->sanitizeFecha($_POST['fechaFin'] ?? null);

        if (empty($grafico)) {
            header('Location: index.php?controller=admin&action=reportes&error=No+se+recibi%C3%B3+el+gr%C3%A1fico');
            exit;
        }

        $grafico = trim($grafico);
        $prefijo = 'data:image/png;base64,';
        if (strpos($grafico, $prefijo) === 0) {
            $grafico = substr($grafico, strlen($prefijo));
        }

        $imagen = base64_decode($grafico);
        if ($imagen === false) {
            header('Location: index.php?controller=admin&action=reportes&error=No+se+pudo+procesar+el+gr%C3%A1fico');
            exit;
        }

        $dompdfBootstrap = __DIR__ . '/../libreria/dompdf-3.1.2/vendor/autoload.php';
        if (file_exists($dompdfBootstrap)) {
            require_once $dompdfBootstrap;
        } else {
            $this->registrarAutoloadDompdf();
        }

        if (!class_exists(\Dompdf\Dompdf::class)) {
            header('Location: index.php?controller=admin&action=reportes&error=Dompdf+no+est%C3%A1+disponible');
            exit;
        }

        $imagenBase64 = 'data:image/png;base64,' . base64_encode($imagen);

        $rangoTexto = 'Rango consultado: ' . ($fechaInicio ?? 'Sin inicio') . ' al ' . ($fechaFin ?? 'Sin fin');

        $html = '<html><head><meta charset="UTF-8"><style>body{font-family: DejaVu Sans, sans-serif;margin:20px;}h1{color:#1f2937;}p{font-size:12px;color:#4b5563;}</style></head><body>' .
            '<h1>Gráfico de acciones por usuario</h1>' .
            '<p>' . htmlspecialchars($rangoTexto, ENT_QUOTES, 'UTF-8') . '</p>' .
            '<img src="' . $imagenBase64 . '" style="width:100%;max-width:700px;" alt="Gráfico de acciones" />' .
            '</body></html>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('grafico_historial.pdf');
        exit;
    }

    private function registrarAutoloadDompdf(): void
    {
        $basePath = __DIR__ . '/../libreria/dompdf-3.1.2';

        if (!is_dir($basePath)) {
            return;
        }

        spl_autoload_register(static function ($class) use ($basePath) {
            $prefixes = [
                'Dompdf\\' => $basePath . '/src/',
                'Dompdf\\Tests\\' => $basePath . '/tests/',
            ];

            foreach ($prefixes as $prefix => $dir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relativeClass = substr($class, $len);
                $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }

            if ($class === 'FontLib\\Autoloader') {
                $archivo = $basePath . '/lib/php-font-lib/src/FontLib/Autoloader.php';
                if (file_exists($archivo)) {
                    require_once $archivo;
                }
                return;
            }

            $extraPrefixes = [
                'FontLib\\' => $basePath . '/lib/php-font-lib/src/',
                'Svg\\' => $basePath . '/lib/php-svg-lib/src/',
                'Masterminds\\HTML5\\' => $basePath . '/lib/html5-php/src/HTML5/',
            ];

            foreach ($extraPrefixes as $prefix => $dir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relativeClass = substr($class, $len);
                $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
                return;
            }
        }, true, true);
    }

    public function empresas()
    {
        $this->ensureSession();

        $empresaModel = new Empresa();

        $categoriaFiltro = isset($_GET['categoria']) ? trim((string) $_GET['categoria']) : 'todas';
        $estadoFiltro = isset($_GET['estado']) ? trim((string) $_GET['estado']) : 'activos';

        $estadosPermitidos = ['activos', 'inactivos', 'todos'];
        if (!in_array($estadoFiltro, $estadosPermitidos, true)) {
            $estadoFiltro = 'activos';
        }

        $categorias = $empresaModel->obtenerCategorias();
        if ($categoriaFiltro !== 'todas' && !in_array($categoriaFiltro, $categorias, true)) {
            $categoriaFiltro = 'todas';
        }

        $empresas = $empresaModel->listarEmpresas($categoriaFiltro, $estadoFiltro);

        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../../views/vistas/administrador/empresas.php';
    }

    public function convocatorias(): void
    {
        $this->ensureSession();

        $convocatoriaModel = new Convocatoria();

        $modalidades = $convocatoriaModel->listarModalidades();
        $jornadas = $convocatoriaModel->listarJornadas();

        $idModalidad = isset($_GET['modalidad']) && $_GET['modalidad'] !== ''
            ? (int) $_GET['modalidad']
            : null;
        $idJornada = isset($_GET['jornada']) && $_GET['jornada'] !== ''
            ? (int) $_GET['jornada']
            : null;
        $estadoFiltro = isset($_GET['estado']) && $_GET['estado'] !== ''
            ? (int) $_GET['estado']
            : '';
        $buscar = trim($_GET['buscar'] ?? '');

        $filtros = [
            'idModalidad' => $idModalidad,
            'idJornada'   => $idJornada,
            'estado'      => $estadoFiltro === '' ? null : $estadoFiltro,
            'buscar'      => $buscar !== '' ? $buscar : null,
        ];

        $convocatorias = $convocatoriaModel->listarParaAdmin($filtros);

        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        require_once __DIR__ . '/../../views/vistas/administrador/convocatorias.php';
    }

    public function actualizarEstadoConvocatoria(): void
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=convocatorias');
            exit;
        }

        $idConvocatoria = isset($_POST['idConvocatoria']) ? (int) $_POST['idConvocatoria'] : 0;
        $estado = $_POST['estado'] ?? '';

        if ($idConvocatoria <= 0 || !in_array($estado, ['0', '1'], true)) {
            header('Location: index.php?controller=admin&action=convocatorias&error=Solicitud+inv%C3%A1lida');
            exit;
        }

        $estadoNuevo = (int) $estado;

        $convocatoriaModel = new Convocatoria();
        $resultado = $convocatoriaModel->cambiarEstado($idConvocatoria, $estadoNuevo, $_SESSION['idUsuario'] ?? null);

        $params = [];

        $modalidadFiltro = $_POST['f_modalidad'] ?? '';
        $jornadaFiltro = $_POST['f_jornada'] ?? '';
        $estadoFiltro = $_POST['f_estado'] ?? '';
        $buscarFiltro = trim($_POST['f_buscar'] ?? '');

        if ($modalidadFiltro !== '') {
            $params['modalidad'] = (int) $modalidadFiltro;
        }

        if ($jornadaFiltro !== '') {
            $params['jornada'] = (int) $jornadaFiltro;
        }

        if ($estadoFiltro !== '') {
            $params['estado'] = (int) $estadoFiltro;
        }

        if ($buscarFiltro !== '') {
            $params['buscar'] = $buscarFiltro;
        }

        $redirect = 'index.php?controller=admin&action=convocatorias';

        if ($resultado) {
            $accion = $estadoNuevo === 1 ? 'Activó convocatoria' : 'Desactivó convocatoria';
            $usuarioAuditoria = $_SESSION['idUsuario'] ?? null;

            if (!empty($usuarioAuditoria)) {
                $usuarioModel = new Usuario();
                $usuarioModel->registrarHistorial($usuarioAuditoria, $accion, 'Convocatorias', $idConvocatoria);
            }

            $params['mensaje'] = 'Estado actualizado correctamente';
        } else {
            $params['error'] = 'No se pudo actualizar el estado de la convocatoria';
        }

        if (!empty($params)) {
            $redirect .= '&' . http_build_query($params);
        }

        header('Location: ' . $redirect);
        exit;
    }

    public function cambiarEstadoEmpresa()
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=empresas');
            exit;
        }

        $idEmpresa = (int) ($_POST['idEmpresa'] ?? 0);
        $accion = $_POST['accion'] ?? '';

        $estado = null;
        if ($accion === 'activar') {
            $estado = 1;
        } elseif ($accion === 'desactivar') {
            $estado = 0;
        }

        if ($idEmpresa <= 0 || $estado === null) {
            header('Location: index.php?controller=admin&action=empresas&error=' . urlencode('Solicitud inválida.'));
            exit;
        }

        $empresaModel = new Empresa();
        $usuarioModel = new Usuario();

        $resultado = $empresaModel->actualizarEstado($idEmpresa, $estado, $_SESSION['idUsuario'] ?? null);

        if ($resultado && !empty($_SESSION['idUsuario'])) {
            $accionHistorial = $estado === 1 ? 'Activó empresa' : 'Desactivó empresa';
            $usuarioModel->registrarHistorial($_SESSION['idUsuario'], $accionHistorial, 'Empresas', $idEmpresa);
        }

        if ($resultado) {
            $mensaje = $estado === 1 ? 'Empresa activada correctamente.' : 'Empresa desactivada correctamente.';
            header('Location: index.php?controller=admin&action=empresas&mensaje=' . urlencode($mensaje));
            exit;
        }

        header('Location: index.php?controller=admin&action=empresas&error=' . urlencode('No se pudo actualizar el estado de la empresa.'));
        exit;
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