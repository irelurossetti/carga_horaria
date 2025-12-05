<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationCriteria;
use App\Models\AcademicPeriod;
use App\Models\Subject;
use App\Models\Group;

class EvaluationCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el primer periodo académico
        $period = AcademicPeriod::first();
        
        if (!$period) {
            $this->command->warn('No hay periodos académicos. Crea uno primero.');
            return;
        }

        // Obtener las primeras 3 materias
        $subjects = Subject::take(3)->get();
        
        if ($subjects->isEmpty()) {
            $this->command->warn('No hay materias. Crea algunas primero.');
            return;
        }

        $this->command->info('Creando criterios de evaluación de prueba...');

        foreach ($subjects as $subject) {
            // Criterios estándar para cada materia
            $criteria = [
                [
                    'name' => 'Parcial 1',
                    'weight' => 25,
                    'description' => 'Primer examen parcial',
                    'evaluation_date' => now()->addDays(30)->format('Y-m-d'),
                ],
                [
                    'name' => 'Parcial 2',
                    'weight' => 25,
                    'description' => 'Segundo examen parcial',
                    'evaluation_date' => now()->addDays(60)->format('Y-m-d'),
                ],
                [
                    'name' => 'Trabajos Prácticos',
                    'weight' => 20,
                    'description' => 'Promedio de trabajos prácticos',
                    'evaluation_date' => null,
                ],
                [
                    'name' => 'Proyecto Final',
                    'weight' => 20,
                    'description' => 'Proyecto integrador final',
                    'evaluation_date' => now()->addDays(90)->format('Y-m-d'),
                ],
                [
                    'name' => 'Participación',
                    'weight' => 10,
                    'description' => 'Participación en clase y asistencia',
                    'evaluation_date' => null,
                ],
            ];

            foreach ($criteria as $criterion) {
                \DB::table('evaluation_criteria')->insert([
                    'name' => $criterion['name'],
                    'weight' => $criterion['weight'],
                    'period_id' => $period->id,
                    'subject_id' => $subject->id,
                    'group_id' => null, // Aplica a todos los grupos
                    'description' => $criterion['description'],
                    'evaluation_date' => $criterion['evaluation_date'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info("✓ Criterios creados para: {$subject->name}");
        }

        $this->command->info('✅ Criterios de evaluación creados exitosamente!');
    }
}
