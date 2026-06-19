<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SensorModel;
use App\Models\ProductionModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends ResourceController
{
    public function pdf()
    {
        $productionModel = new ProductionModel();
        $data = $productionModel->orderBy('tanggal', 'DESC')->findAll();

        $html = '<h2>Laporan Produksi Maggot</h2><table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>Tanggal</th><th>Berat Maggot (Kg)</th><th>Berat Pakan (Kg)</th></tr>';
        foreach ($data as $row) {
            $html .= "<tr><td>{$row['tanggal']}</td><td>{$row['berat_maggot']}</td><td>{$row['berat_pakan']}</td></tr>";
        }
        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("laporan_produksi.pdf", array("Attachment" => true));
    }

    public function excel()
    {
        $productionModel = new ProductionModel();
        $data = $productionModel->orderBy('tanggal', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Berat Maggot (Kg)');
        $sheet->setCellValue('C1', 'Berat Pakan (Kg)');

        $rowNum = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['tanggal']);
            $sheet->setCellValue('B' . $rowNum, $row['berat_maggot']);
            $sheet->setCellValue('C' . $rowNum, $row['berat_pakan']);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_produksi.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
