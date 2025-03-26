<?php 
 
require_once __DIR__ . '/../vendor/autoload.php'; 
 
class pdf_controller { 
    public function generarPDF() { 
        // Recoger datos de la solicitud GET o POST
        $requestId = $_GET['id'] ?? '';
        
        // Verificar si los datos necesarios se pasaron por GET o POST
        $datos = [
            'requestId' => $requestId,
            'fecha_solicitud' => $_GET['fecha_solicitud'] ?? $_POST['fecha_solicitud'] ?? date('d/m/Y'),
            'nombre_empleado' => $_GET['nombre_empleado'] ?? $_POST['nombre_empleado'] ?? '',
            'no_empleado' => $_GET['no_empleado'] ?? $_POST['no_empleado'] ?? '',
            'departamento' => $_GET['departamento'] ?? $_POST['departamento'] ?? '',
            'tiempo_servicio' => $_GET['tiempo_servicio'] ?? $_POST['tiempo_servicio'] ?? '',
            'fecha_ingreso' => $_GET['fecha_ingreso'] ?? $_POST['fecha_ingreso'] ?? '',
            'dias_disponibles' => $_GET['dias_disponibles'] ?? $_POST['dias_disponibles'] ?? '',
            'dias_corresponden' => $_GET['dias_corresponden'] ?? $_POST['dias_corresponden'] ?? '',
            'dias_solicitados' => $_GET['dias_solicitados'] ?? $_POST['dias_solicitados'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? $_POST['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? $_POST['fecha_hasta'] ?? ''
        ];

        // Crear nuevo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configurar metadatos del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Solicitud de Vacaciones');
        
        // Configurar márgenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        
        // Añadir página
        $pdf->AddPage();
        
        // Título del documento
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'SOLICITUD DE VACACIONES', 0, 1, 'C');
        
        // Código y revisión
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 5, 'CÓDIGO: R-SRH-020   REV: 01   EMISIÓN: 16-03-2023', 0, 1, 'C');
        
        // Línea de separación
        $pdf->Ln(5);
        $pdf->Line(PDF_MARGIN_LEFT, $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
        $pdf->Ln(5);
        
        // Sección: INFORMACIÓN DEL SOLICITANTE
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL SOLICITANTE', 0, 1, 'L');
        
        // Campos de información
        $pdf->SetFont('helvetica', '', 10);
        $campos = [
            'FECHA DE SOLICITUD' => $datos['fecha_solicitud'],
            'NOMBRE DEL EMPLEADO' => $datos['nombre_empleado'],
            'No. DE EMPLEADO' => $datos['no_empleado'],
            'DEPARTAMENTO' => $datos['departamento'],
            'TIEMPO DE SERVICIO' => $datos['tiempo_servicio'],
            'FECHA DE INGRESO' => $datos['fecha_ingreso']
        ];
        
        foreach ($campos as $etiqueta => $valor) {
            $pdf->Cell(70, 6, $etiqueta . ':', 1);
            $pdf->Cell(0, 6, $valor, 1, 1);
        }
        
        // Sección de detalles de vacaciones
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'DETALLES DE VACACIONES', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $campos_vacaciones = [
            'DIAS DISPONIBLES' => $datos['dias_disponibles'],
            'DIAS QUE CORRESPONDEN' => $datos['dias_corresponden'],
            'DIAS SOLICITADOS' => $datos['dias_solicitados'],
            'DESDE' => $datos['fecha_desde'],
            'HASTA' => $datos['fecha_hasta'],
            'TRAMITAR ENCARGO' => 'No'
        ];
        
        foreach ($campos_vacaciones as $etiqueta => $valor) {
            $pdf->Cell(70, 6, $etiqueta . ':', 1);
            $pdf->Cell(0, 6, $valor, 1, 1);
        }
        
        // Resto del código de generación de PDF (igual que antes)
        
        // Enviar el PDF
        $pdf->Output('solicitud_vacaciones.pdf', 'I');
    }
}