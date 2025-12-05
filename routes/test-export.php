<?php

use Illuminate\Support\Facades\Route;
use App\Models\Group;
use App\Models\EvaluationCriteria;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradesExport;

// Ruta de prueba simple para PDF
Route::get('/test/pdf/{groupId}', function($groupId) {
    try {
        // Crear grupo de prueba si no existe
        $group = Group::find($groupId);
        if (!$group) {
            $group = (object)[
                'id' => $groupId,
                'name' => 'Grupo de Prueba',
                'subject' => (object)['name' => 'Materia de Prueba']
            ];
        } else {
            $group->load('subject');
        }

        // Datos de prueba
        $criteria = collect([
            (object)['id' => 1, 'name' => 'Parcial 1', 'weight' => 25],
            (object)['id' => 2, 'name' => 'Parcial 2', 'weight' => 25],
            (object)['id' => 3, 'name' => 'Trabajos', 'weight' => 20],
            (object)['id' => 4, 'name' => 'Proyecto', 'weight' => 20],
            (object)['id' => 5, 'name' => 'Participación', 'weight' => 10],
        ]);

        $students = collect([
            (object)[
                'id' => 1,
                'name' => 'Juan Pérez',
                'registration_number' => '2021001',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 80],
                    ['criteria_id' => 2, 'score' => 85],
                    ['criteria_id' => 3, 'score' => 90],
                    ['criteria_id' => 4, 'score' => 95],
                    ['criteria_id' => 5, 'score' => 100],
                ]),
                'final_grade' => 88.75
            ],
            (object)[
                'id' => 2,
                'name' => 'María García',
                'registration_number' => '2021002',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 75],
                    ['criteria_id' => 2, 'score' => 80],
                    ['criteria_id' => 3, 'score' => 85],
                    ['criteria_id' => 4, 'score' => 90],
                    ['criteria_id' => 5, 'score' => 95],
                ]),
                'final_grade' => 83.75
            ],
            (object)[
                'id' => 3,
                'name' => 'Carlos López',
                'registration_number' => '2021003',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 70],
                    ['criteria_id' => 2, 'score' => 75],
                    ['criteria_id' => 3, 'score' => 80],
                    ['criteria_id' => 4, 'score' => 85],
                    ['criteria_id' => 5, 'score' => 90],
                ]),
                'final_grade' => 78.75
            ],
        ]);

        $stats = [
            'average' => 83.75,
            'passed' => 3,
            'failed' => 0,
        ];

        $pdf = Pdf::loadView('reports.grades-pdf', [
            'group' => $group,
            'criteria' => $criteria,
            'students' => $students,
            'stats' => $stats,
            'date' => \Carbon\Carbon::now()->format('d/m/Y H:i')
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'encoding' => 'UTF-8'
        ]);

        return $pdf->download('calificaciones_prueba_' . date('Y-m-d') . '.pdf');
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Ruta de prueba simple para Excel
Route::get('/test/excel/{groupId}', function($groupId) {
    try {
        // Crear grupo de prueba si no existe
        $group = Group::find($groupId);
        if (!$group) {
            $group = (object)[
                'id' => $groupId,
                'name' => 'Grupo de Prueba',
                'subject' => (object)['name' => 'Materia de Prueba']
            ];
        } else {
            $group->load('subject');
        }

        // Datos de prueba
        $criteria = collect([
            (object)['id' => 1, 'name' => 'Parcial 1', 'weight' => 25],
            (object)['id' => 2, 'name' => 'Parcial 2', 'weight' => 25],
            (object)['id' => 3, 'name' => 'Trabajos', 'weight' => 20],
            (object)['id' => 4, 'name' => 'Proyecto', 'weight' => 20],
            (object)['id' => 5, 'name' => 'Participación', 'weight' => 10],
        ]);

        $students = collect([
            (object)[
                'id' => 1,
                'name' => 'Juan Pérez',
                'registration_number' => '2021001',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 80],
                    ['criteria_id' => 2, 'score' => 85],
                    ['criteria_id' => 3, 'score' => 90],
                    ['criteria_id' => 4, 'score' => 95],
                    ['criteria_id' => 5, 'score' => 100],
                ]),
                'final_grade' => 88.75
            ],
            (object)[
                'id' => 2,
                'name' => 'María García',
                'registration_number' => '2021002',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 75],
                    ['criteria_id' => 2, 'score' => 80],
                    ['criteria_id' => 3, 'score' => 85],
                    ['criteria_id' => 4, 'score' => 90],
                    ['criteria_id' => 5, 'score' => 95],
                ]),
                'final_grade' => 83.75
            ],
            (object)[
                'id' => 3,
                'name' => 'Carlos López',
                'registration_number' => '2021003',
                'grades' => collect([
                    ['criteria_id' => 1, 'score' => 70],
                    ['criteria_id' => 2, 'score' => 75],
                    ['criteria_id' => 3, 'score' => 80],
                    ['criteria_id' => 4, 'score' => 85],
                    ['criteria_id' => 5, 'score' => 90],
                ]),
                'final_grade' => 78.75
            ],
        ]);

        return Excel::download(
            new GradesExport($group, $criteria, $students),
            'calificaciones_prueba_' . date('Y-m-d') . '.xlsx'
        );
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
