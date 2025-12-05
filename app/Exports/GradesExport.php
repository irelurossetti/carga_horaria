<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GradesExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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

    public function collection()
    {
        $rows = [];
        
        foreach ($this->students as $student) {
            $row = [
                $student->name,
                $student->registration_number ?? 'N/A',
            ];
            
            // Agregar calificaciones por criterio
            foreach ($this->criteria as $criterion) {
                $grade = collect($student->grades)->firstWhere('criteria_id', $criterion->id);
                $row[] = $grade ? $grade->score : '';
            }
            
            // Agregar nota final y estado
            $finalGrade = $student->final_grade ?? 0;
            $row[] = number_format($finalGrade, 2);
            $row[] = $finalGrade >= 51 ? 'Aprobado' : 'Reprobado';
            
            $rows[] = $row;
        }
        
        return collect($rows);
    }

    public function headings(): array
    {
        $headings = ['Estudiante', 'Código'];
        
        foreach ($this->criteria as $criterion) {
            $headings[] = $criterion->name . ' (' . $criterion->weight . '%)';
        }
        
        $headings[] = 'Nota Final';
        $headings[] = 'Estado';
        
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '881F34'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Ajustar ancho de columnas
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return [];
    }

    public function title(): string
    {
        return 'Calificaciones';
    }
}
