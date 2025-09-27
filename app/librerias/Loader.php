<?php
namespace Librerias;

/**
 * Cargador auxiliar para las librerías externas utilizadas en los reportes.
 */
class Loader
{
    /**
     * Intenta cargar PhpSpreadsheet desde las ubicaciones conocidas.
     */
    public static function cargarPhpSpreadsheet(): bool
    {
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return true;
        }

        $rutas = [
            __DIR__ . '/PhpSpreadsheet-5.1.0/src/Bootstrap.php',
            __DIR__ . '/PhpSpreadsheet/bootstrap.php',
            __DIR__ . '/PhpSpreadsheet/vendor/autoload.php',
            __DIR__ . '/../../vendor/autoload.php',
        ];

        foreach ($rutas as $ruta) {
            if (self::requireIfExists($ruta) && class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                return true;
            }
        }

        return class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class);
    }

    /**
     * Intenta cargar Dompdf desde las ubicaciones conocidas.
     */
    public static function cargarDompdf(): bool
    {
        if (class_exists(\Dompdf\Dompdf::class)) {
            return true;
        }

        $rutas = [
            __DIR__ . '/dompdf-3.1.2/vendor/autoload.php',
            __DIR__ . '/dompdf/vendor/autoload.php',
            __DIR__ . '/../../vendor/autoload.php',
        ];

        foreach ($rutas as $ruta) {
            if (self::requireIfExists($ruta) && class_exists(\Dompdf\Dompdf::class)) {
                return true;
            }
        }

        $basePath = __DIR__ . '/dompdf-3.1.2';
        if (is_dir($basePath)) {
            self::registrarAutoloadDompdf($basePath);
        }

        return class_exists(\Dompdf\Dompdf::class);
    }

    private static function requireIfExists(string $ruta): bool
    {
        if (file_exists($ruta)) {
            require_once $ruta;
            return true;
        }

        return false;
    }

    private static function registrarAutoloadDompdf(string $basePath): void
    {
        spl_autoload_register(static function ($class) use ($basePath) {
            $prefijos = [
                'Dompdf\\' => $basePath . '/src/',
                'Dompdf\\Tests\\' => $basePath . '/tests/',
            ];

            foreach ($prefijos as $prefijo => $directorio) {
                $longitud = strlen($prefijo);
                if (strncmp($prefijo, $class, $longitud) !== 0) {
                    continue;
                }

                $claseRelativa = substr($class, $longitud);
                $archivo = $directorio . str_replace('\\', '/', $claseRelativa) . '.php';

                if (file_exists($archivo)) {
                    require_once $archivo;
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

            $prefijosExtras = [
                'FontLib\\' => $basePath . '/lib/php-font-lib/src/',
                'Svg\\' => $basePath . '/lib/php-svg-lib/src/',
                'Masterminds\\HTML5\\' => $basePath . '/lib/html5-php/src/HTML5/',
            ];

            foreach ($prefijosExtras as $prefijo => $directorio) {
                $longitud = strlen($prefijo);
                if (strncmp($prefijo, $class, $longitud) !== 0) {
                    continue;
                }

                $claseRelativa = substr($class, $longitud);
                $archivo = $directorio . str_replace('\\', '/', $claseRelativa) . '.php';
                if (file_exists($archivo)) {
                    require_once $archivo;
                }
                return;
            }
        }, true, true);
    }
}