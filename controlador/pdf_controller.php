<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Knp\Snappy\Pdf;

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

        // Comenzar captura de salida
        ob_start();
        
        // Pasar datos a la plantilla
        extract($datosCompletos);
        
        // Incluir la plantilla HTML (asegúrate que la ruta sea correcta)
        include __DIR__ . '/../vistas/pdf.php';
        
        $html = ob_get_clean();

        echo $html;
        die();


        // Crea una instancia de Snappy PDF, especificando la ruta al ejecutable de wkhtmltopdf
        $snappy = new \Knp\Snappy\Pdf("C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe");

        // Opciones adicionales (por ejemplo, habilitar el acceso a archivos locales si es necesario)
        $snappy->setOption('enable-local-file-access', true);

        // Configurar las cabeceras para PDF y forzar la descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="solicitud_vacaciones.pdf"');

        // Genera el PDF a partir del HTML y lo imprime
        echo $snappy->getOutputFromHtml($html);
        exit;
    }
}
?>
