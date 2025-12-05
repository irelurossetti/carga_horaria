<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SyllabusTopic;
use App\Models\Subject;

class SyllabusTopicsSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener algunas materias para agregar temas
        $subjects = Subject::take(3)->get();

        if ($subjects->isEmpty()) {
            $this->command->warn('No hay materias en la base de datos. Ejecuta primero el seeder de materias.');
            return;
        }

        foreach ($subjects as $index => $subject) {
            // Crear 5-8 temas por materia
            $topicsCount = rand(5, 8);
            
            for ($i = 1; $i <= $topicsCount; $i++) {
                SyllabusTopic::create([
                    'subject_id' => $subject->id,
                    'unit_name' => 'Unidad ' . $i,
                    'topic_description' => $this->getTopicDescription($subject->name, $i),
                    'order_index' => $i
                ]);
            }
        }

        $this->command->info('Temas del sílabo creados exitosamente.');
    }

    private function getTopicDescription($subjectName, $unit): string
    {
        $descriptions = [
            1 => 'Introducción y conceptos fundamentales',
            2 => 'Fundamentos teóricos y principios básicos',
            3 => 'Aplicaciones prácticas y ejercicios',
            4 => 'Técnicas avanzadas y metodologías',
            5 => 'Casos de estudio y análisis',
            6 => 'Implementación y desarrollo de proyectos',
            7 => 'Evaluación y optimización',
            8 => 'Integración y conclusiones finales'
        ];

        return $descriptions[$unit] ?? 'Contenido de la unidad ' . $unit;
    }
}
