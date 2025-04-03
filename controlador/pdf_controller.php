<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

class pdf_controller {
    public function generarPDF() {
        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'requestId' => $datos['id'] ?? '',
            'fecha_solicitud' => $datos['fecha_solicitud'] ?? date('d/m/Y'),
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => $datos['departamento'] ?? '',
            'tiempo_servicio' => $datos['tiempo_servicio'] ?? '',
            'fecha_ingreso' => $datos['fecha_ingreso'] ?? '',
            'dias_disponibles' => $datos['dias_disponibles'] ?? '',
            'dias_corresponden' => $datos['dias_corresponden'] ?? '',
            'dias_solicitados' => $datos['dias_solicitados'] ?? '',
            'fecha_desde' => $datos['fecha_desde'] ?? '',
            'fecha_hasta' => $datos['fecha_hasta'] ?? '',
            'fecha_regreso' => $datos['fecha_regreso'] ?? ''
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/pdf.html';
        $html = ob_get_clean();

        // Guardar HTML en un archivo temporal
        $htmlPath = __DIR__ . '/../vistas/temp.html';
        file_put_contents($htmlPath, $html);

        // Ruta de salida del PDF
        $pdfPath = __DIR__ . '/../vistas/solicitud_vacaciones.pdf';

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
}

?>
