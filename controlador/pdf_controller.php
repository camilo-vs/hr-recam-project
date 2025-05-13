<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class pdf_controller
{
    public function formatearFecha($fecha)
    {
        if (empty($fecha)) {
            return '';
        }
        // Lista de posibles formatos
        $formatos = [
            'd-m-Y H:i:s',
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

    private function generarPdfConDompdf($html, $nombreArchivo)
    {
        // Configurar opciones de DomPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$nombreArchivo.'"');
        echo $dompdf->output();
        exit;
    }

    public function generarPDF()
    {
        // Recoger datos del POST
        $datos = $_POST;
        $dias_disponibles_restantes = 0;

        if (isset($datos['dias_disponibles'], $datos['dias_solicitados']) &&
            is_numeric($datos['dias_disponibles']) && is_numeric($datos['dias_solicitados'])) {
            $dias_disponibles_restantes = $datos['dias_disponibles'] - $datos['dias_solicitados'];
        }
        
        // Extraer datos con valores por defecto
        $datosCompletos = [
            'requestId' => $datos['id'] ?? '',
            'fecha_solicitud_dia' => $datos['fecha_solicitud_dia'] ?? '',
            'fecha_solicitud_mes' => $datos['fecha_solicitud_mes'] ?? '',
            'fecha_solicitud_anio' => $datos['fecha_solicitud_anio'] ?? '',

            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' =>  strtoupper($datos['departamento'] ?? ''),
            'tiempo_servicio_anio' => $datos['tiempo_servicio_anio'] ?? '',
            'tiempo_servicio_mes' => $datos['tiempo_servicio_mes'] ?? '',

            'fecha_ingreso_dia' => $datos['fecha_ingreso_dia'] ?? '',
            'fecha_ingreso_mes' => $datos['fecha_ingreso_mes'] ?? '',
            'fecha_ingreso_anio' => $datos['fecha_ingreso_anio'] ?? '',
          
            'dias_disponibles' =>  $dias_disponibles_restantes,
            'dias_corresponden' => $datos['dias_corresponden'] ?? '',
            'dias_solicitados' => $datos['dias_solicitados'] ?? '',
 
            'fecha_desde_dia' => $datos['fecha_desde_dia'] ?? '',
            'fecha_desde_mes' => $datos['fecha_desde_mes'] ?? '',
            'fecha_desde_anio' => $datos['fecha_desde_anio'] ?? '',
        
            'fecha_hasta_dia' => $datos['fecha_hasta_dia'] ?? '',
            'fecha_hasta_mes' => $datos['fecha_hasta_mes'] ?? '',
            'fecha_hasta_anio' => $datos['fecha_hasta_anio'] ?? '',
          
            'fecha_regreso_dia' => $datos['fecha_regreso_dia'] ?? '',
            'fecha_regreso_mes' => $datos['fecha_regreso_mes'] ?? '',
            'fecha_regreso_anio' => $datos['fecha_regreso_anio'] ?? ''
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdf.html';
        $html = ob_get_clean();

        // Generar nombre del archivo
        $nombreArchivo = 'solicitud_vacaciones_' . $datosCompletos['no_empleado'] . '.pdf';
        
        // Generar PDF con DomPDF
        $this->generarPdfConDompdf($html, $nombreArchivo);
    }

    public function generarPDFI()
    {
        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'fecha_solicitud' => $this->formatearFecha($datos['fecha_solicitud']) ?? date('d/m/Y'),
            'fecha_solicitada' => $this->formatearFecha($datos['fecha_solicitada']) ?? '',
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => strtoupper($datos['departamento'] ?? '')
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdfI.html';
        $html = ob_get_clean();

        // Generar nombre del archivo
        $nombreArchivo = 'solicitud_I_' . $datosCompletos['no_empleado'] . '.pdf';
        
        // Generar PDF con DomPDF
        $this->generarPdfConDompdf($html, $nombreArchivo);
    }

    public function generarPDFS()
    {
        // Recoger datos del POST
        $datos = $_POST;

        // Extraer datos con valores por defecto
        $datosCompletos = [
            'fecha_solicitud' => $this->formatearFecha($datos['fecha_solicitud']) ?? date('d/m/Y'),
            'fecha_solicitada' => $this->formatearFecha($datos['fecha_solicitada']) ?? '',
            'nombre_empleado' => $datos['nombre_empleado'] ?? '',
            'no_empleado' => $datos['no_empleado'] ?? '',
            'departamento' => strtoupper($datos['departamento'] ?? '')
        ];

        // Capturar HTML de la plantilla
        ob_start();
        extract($datosCompletos);
        include __DIR__ . '/../vistas/PDF/pdfS.html';
        $html = ob_get_clean();

        // Generar nombre del archivo
        $nombreArchivo = 'solicitud_S_' . $datosCompletos['no_empleado'] . '.pdf';
        
        // Generar PDF con DomPDF
        $this->generarPdfConDompdf($html, $nombreArchivo);
    }
}