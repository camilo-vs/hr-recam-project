<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

class pdf_controller {
    public function generarPDF() {

        function formatearFecha($fecha) {
            if (empty($fecha)) return '';
        
            // Lista de posibles formatos
            $formatos = [
                'Y-m-d',         // ej: 2025-04-09
                'Y-m-d H:i:s',   // ej: 2025-04-09 14:00:00
                'd/m/Y H:i:s',   // ej: 13/04/2019 13:52:41
                'd/m/Y'          // ej: 13/04/2019
            ];
        
            foreach ($formatos as $formato) {
                $dt = \DateTime::createFromFormat($formato, $fecha);
                if ($dt !== false) {
                    return $dt->format('d-m-Y');
                }
            }
        
            return ''; // Si ningún formato fue válido
        }

        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'requestId' => $datos['id'] ?? '',
            'fecha_solicitud' => formatearFecha($datos['fecha_solicitud'] ?? date('Y-m-d')),
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => $datos['departamento'] ?? '',
            'tiempo_servicio' => $datos['tiempo_servicio'] ?? '',
            'fecha_ingreso' =>formatearFecha($datos['fecha_ingreso'] ?? date('Y-m-d')),
            'dias_disponibles' => $datos['dias_disponibles'] ?? '',
            'dias_corresponden' => $datos['dias_corresponden'] ?? '',
            'dias_solicitados' => $datos['dias_solicitados'] ?? '',
            'fecha_desde' => formatearFecha($datos['fecha_desde'] ?? date('Y-m-d')),
            'fecha_hasta' => formatearFecha($datos['fecha_hasta'] ?? date('Y-m-d')),
            'fecha_regreso' => formatearFecha($datos['fecha_regreso'] ?? date('Y-m-d'))
        ];

        var_dump($datosCompletos['fecha_ingreso']);

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdf.html';
        $html = ob_get_clean();

        // Guardar HTML en un archivo temporal
        $htmlPath = __DIR__ . '/../vistas/PDF/tempPDF/temp.html';
        file_put_contents($htmlPath, $html);

        // Ruta de salida del PDF
        $pdfPath = __DIR__ . '/../vistas/PDF/generatedPDF/solicitud_vacaciones' . $datosCompletos['no_empleado'] . '-' . $datosCompletos['fecha_solicitud'] . '.pdf';


        // Ejecutar Puppeteer con Node.js para generar el PDF
        $script = __DIR__ . '/../js/generate_pdf.js';
        $command = "node \"$script\" \"$htmlPath\" \"$pdfPath\"";
        exec($command, $output, $returnVar);

        // Verificar si el PDF se generó correctamente
        if ($returnVar !== 0 || !file_exists($pdfPath)) {
            die("Error al generar el PDF. Salida: " . implode("\n", $output));
        }

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="solicitud_vacaciones.pdf"');
        readfile($pdfPath);
        exit;
    }

    public function generarPDFI() {
        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'fecha_solicitud' => formatearFecha($datos['fecha_solicitud']) ?? date('d/m/Y'),
            'fecha_solicitada' => formatearFecha($datos['fecha_solicitada']) ?? '',
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => $datos['departamento'] ?? ''
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdfI.html';
        $html = ob_get_clean();

        // Guardar HTML en un archivo temporal
        $htmlPath = __DIR__ . '/../vistas/PDF/tempPDF/tempI.html';
        file_put_contents($htmlPath, $html);

        // Ruta de salida del PDF
        $pdfPath = __DIR__ . '/../vistas/PDF/generatedPDF/solicitud_I_' . $datosCompletos['no_empleado'] . '-' . $datosCompletos['fecha_solicitud'] . '.pdf';

        // Ejecutar Puppeteer con Node.js para generar el PDF
        $script = __DIR__ . '/../js/generate_pdf.js';
        $command = "node \"$script\" \"$htmlPath\" \"$pdfPath\"";
        exec($command, $output, $returnVar);

        // Verificar si el PDF se generó correctamente
        if ($returnVar !== 0 || !file_exists($pdfPath)) {
            die("Error al generar el PDF. Salida: " . implode("\n", $output));
        }

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="solicitud_I.pdf"');
        readfile($pdfPath);
        exit;
    }

    public function generarPDFS() {
        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'fecha_solicitud' => formatearFecha($datos['fecha_solicitud']) ?? date('d/m/Y'),
            'fecha_solicitada' => formatearFecha($datos['fecha_solicitada']) ?? '',
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => $datos['departamento'] ?? ''
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdfS.html';
        $html = ob_get_clean();

        // Guardar HTML en un archivo temporal
        $htmlPath = __DIR__ . '/../vistas/PDF/tempPDF/tempS.html';
        file_put_contents($htmlPath, $html);

        // Ruta de salida del PDF
        $pdfPath = __DIR__ . '/../vistas/PDF/generatedPDF/solicitud_S_' . $datosCompletos['no_empleado'] . '-' . $datosCompletos['fecha_solicitud'] . '.pdf';

        // Ejecutar Puppeteer con Node.js para generar el PDF
        $script = __DIR__ . '/../js/generate_pdf.js';
        $command = "node \"$script\" \"$htmlPath\" \"$pdfPath\"";
        exec($command, $output, $returnVar);

        // Verificar si el PDF se generó correctamente
        if ($returnVar !== 0 || !file_exists($pdfPath)) {
            die("Error al generar el PDF. Salida: " . implode("\n", $output));
        }

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="solicitud_S.pdf"');
        readfile($pdfPath);
        exit;
    }
}

?>