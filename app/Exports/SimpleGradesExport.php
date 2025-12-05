<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SimpleGradesExport
{
    protected $group;
    protected $criteria;
    protected $students;

    public function __construct($group, $criteria, $students)
    {
        $this->group = $group;
        $this->criteria = $criteria;
        $this->students = $students;
    }

    public function download($filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Calificaciones');

        // Encabezados
        $headers = ['Estudiante', 'Código'];
        foreach ($this->criteria as $criterion) {
            $headers[] = $criterion->name . ' (' . $criterion->weight . '%)';
        }
        $headers[] = 'Nota Final';
        $headers[] = 'Estado';

        // Escribir encabezados
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '881F34']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            $col++;
        }

        // Escribir datos
        $row = 2;
        foreach ($this->students as $student) {
            $col = 'A';
            
            // Nombre y código
            $sheet->setCellValue($col++ . $row, $student->name);
            $sheet->setCellValue($col++ . $row, $student->registration_number ?? 'N/A');
            
            // Calificaciones por criterio
            foreach ($this->criteria as $criterion) {
                $grade = collect($student->grades)->firstWhere('criteria_id', $criterion->id);
                $score = $grade ? ($grade['score'] ?? $grade->score ?? '') : '';
                $sheet->setCellValue($col++ . $row, $score);
            }
            
            // Nota final y estado
            $finalGrade = $student->final_grade ?? 0;
            $sheet->setCellValue($col++ . $row, number_format($finalGrade, 2));
            $sheet->setCellValue($col++ . $row, $finalGrade >= 51 ? 'Aprobado' : 'Reprobado');
            
            $row++;
        }

        // Auto-ajustar columnas
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Crear writer y descargar
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
