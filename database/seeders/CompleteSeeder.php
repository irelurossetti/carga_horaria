<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteSeeder extends Seeder
{
    private $teacherIds = [];
    private $studentIds = [];
    private $subjectIds = [];
    private $groupIds = [];
    private $roomIds = [];
    private $periodIds = [];
    private $assignmentIds = [];
    private $scheduleIds = [];
    private $userIds = [];
    
    public function run(): void
    {
        echo "🚀 Iniciando población completa de base de datos con 30+ registros...\n\n";
        
        $this->createRoles();
        $this->createPeriods(); // 30 periodos
        $this->createRooms(); // 30 aulas
        $this->createSubjects(); // 30 materias
        $this->createTeachers(); // 30 docentes
        $this->createStudents(); // 30 estudiantes
        $this->createGroups(); // 30 grupos
        $this->createTeacherAssignments(); // 30 asignaciones
        $this->createSchedules(); // 30 horarios
        $this->createAttendances(); // 30 asistencias de docentes
        $this->createClassCancellations(); // 30 anulaciones
        $this->createConflicts(); // 30 conflictos
        $this->createReservations(); // 30 reservas
        $this->createAnnouncements(); // 30 anuncios
        $this->createIncidents(); // 30 incidencias
        $this->createActivityLogs(); // 30+ registros de bitácora
        
        echo "\n✅ ¡Base de datos poblada exitosamente!\n";
        $this->showSummary();
    }
    
    private function createRoles()
    {
        echo "👥 Creando roles...\n";
        
        $roles = [
            ['name' => 'ADMIN', 'guard_name' => 'web'],
            ['name' => 'COORDINADOR', 'guard_name' => 'web'],
            ['name' => 'DOCENTE', 'guard_name' => 'web'],
            ['name' => 'ESTUDIANTE', 'guard_name' => 'web'],
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
        
        // Crear usuario admin
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Administrador Sistema',
            'email' => 'admin@ficct.edu.bo',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('role_user')->insert([
            'user_id' => $adminId,
            'role_id' => 1,
        ]);
        
        $this->userIds[] = $adminId;
        
        echo "   ✓ 4 roles + 1 admin creados\n";
    }

    
    private function createPeriods()
    {
        echo "📅 Creando 30 periodos académicos...\n";
        
        for ($i = 1; $i <= 30; $i++) {
            $year = 2020 + floor(($i - 1) / 2);
            $semester = ($i % 2 == 1) ? 1 : 2;
            
            $startMonth = $semester == 1 ? 1 : 7;
            $endMonth = $semester == 1 ? 6 : 12;
            
            $id = DB::table('academic_periods')->insertGetId([
                'code' => "$year-$semester",
                'name' => "Gestión $semester-$year",
                'description' => "Periodo académico $semester del año $year",
                'start_date' => "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-15",
                'end_date' => "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-30",
                'status' => $i == 30 ? 'active' : ($i >= 28 ? 'draft' : 'closed'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->periodIds[] = $id;
        }
        
        echo "   ✓ 30 periodos creados\n";
    }
    
    private function createRooms()
    {
        echo "🏫 Creando 31 aulas (4 pisos)...\n";
        
        // Piso 1: Aulas 11-17 (7 aulas, 90 personas c/u)
        for ($i = 1; $i <= 7; $i++) {
            $id = DB::table('rooms')->insertGetId([
                'name' => 'Aula 1' . $i,
                'capacity' => 90,
                'location' => 'Primer Piso',
                'resources' => json_encode(['projector' => true, 'whiteboard' => true]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->roomIds[] = $id;
        }
        
        // Piso 2: Aulas 21-27 (7 aulas, 90 personas c/u)
        for ($i = 1; $i <= 7; $i++) {
            $id = DB::table('rooms')->insertGetId([
                'name' => 'Aula 2' . $i,
                'capacity' => 90,
                'location' => 'Segundo Piso',
                'resources' => json_encode(['projector' => true, 'whiteboard' => true]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->roomIds[] = $id;
        }
        
        // Piso 3: Aulas 31-38 (8 aulas, 90 personas c/u)
        for ($i = 1; $i <= 5; $i++) {
            $id = DB::table('rooms')->insertGetId([
                'name' => 'Aula 3' . $i,
                'capacity' => 90,
                'location' => 'Tercer Piso',
                'resources' => json_encode(['projector' => true, 'whiteboard' => true]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->roomIds[] = $id;
        }
        
        // Aulas especiales del piso 3
        $id = DB::table('rooms')->insertGetId([
            'name' => 'Laboratorio de Redes 36',
            'capacity' => 90,
            'location' => 'Tercer Piso',
            'resources' => json_encode(['type' => 'Lab Redes', 'network_equipment' => true, 'projector' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->roomIds[] = $id;
        
        $id = DB::table('rooms')->insertGetId([
            'name' => 'FabLab de Redes 37',
            'capacity' => 90,
            'location' => 'Tercer Piso',
            'resources' => json_encode(['type' => 'FabLab', 'fabrication_tools' => true, 'workshop' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->roomIds[] = $id;
        
        $id = DB::table('rooms')->insertGetId([
            'name' => 'Aula 38',
            'capacity' => 90,
            'location' => 'Tercer Piso',
            'resources' => json_encode(['projector' => true, 'whiteboard' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->roomIds[] = $id;
        
        // Piso 4: Laboratorios de PC (40-48)
        $piso4 = [
            ['name' => 'Auditorio 40', 'capacity' => 120, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Auditorio', 'audio_system' => true, 'projector' => true, 'air_conditioning' => true])],
            ['name' => 'Laboratorio PC 41', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Lab PC', 'computers' => 43, 'projector' => true])],
            ['name' => 'Aula 42', 'capacity' => 90, 'location' => 'Cuarto Piso', 'resources' => json_encode(['projector' => true, 'whiteboard' => true])],
            ['name' => 'Laboratorio PC 43', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Lab PC', 'computers' => 25, 'projector' => true])],
            ['name' => 'Laboratorio PC 44', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Lab PC', 'computers' => 43, 'projector' => true])],
            ['name' => 'Laboratorio PC 45', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Lab PC', 'computers' => 43, 'projector' => true])],
            ['name' => 'Laboratorio PC 46', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Lab PC', 'computers' => 43, 'projector' => true])],
            ['name' => 'Depósito 47', 'capacity' => 0, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Depósito', 'storage' => true])],
            ['name' => 'Aula Desarrollo 48', 'capacity' => 60, 'location' => 'Cuarto Piso', 'resources' => json_encode(['type' => 'Desarrollo', 'computers' => true, 'projector' => true])],
        ];
        
        foreach ($piso4 as $room) {
            $room['created_at'] = now();
            $room['updated_at'] = now();
            $id = DB::table('rooms')->insertGetId($room);
            $this->roomIds[] = $id;
        }
        
        echo "   ✓ 31 aulas creadas (Piso 1: 7, Piso 2: 7, Piso 3: 8, Piso 4: 9)\n";
    }
    
    private function createSubjects()
    {
        echo "📚 Creando 30 materias...\n";
        
        $subjects = [
            ['INF-101', 'Introducción a la Programación', 4],
            ['MAT-101', 'Cálculo I', 4],
            ['MAT-102', 'Álgebra Lineal', 4],
            ['FIS-101', 'Física I', 4],
            ['QUI-101', 'Química General', 3],
            ['INF-201', 'Programación Orientada a Objetos', 4],
            ['MAT-201', 'Cálculo II', 4],
            ['FIS-201', 'Física II', 4],
            ['MAT-203', 'Estructuras Discretas', 3],
            ['ING-101', 'Inglés Técnico I', 2],
            ['INF-301', 'Estructura de Datos', 4],
            ['INF-302', 'Base de Datos I', 4],
            ['INF-303', 'Arquitectura de Computadoras', 3],
            ['MAT-301', 'Probabilidad y Estadística', 3],
            ['ING-201', 'Inglés Técnico II', 2],
            ['INF-401', 'Algoritmos Avanzados', 4],
            ['INF-402', 'Base de Datos II', 4],
            ['INF-403', 'Sistemas Operativos', 4],
            ['INF-404', 'Redes de Computadoras I', 3],
            ['INF-405', 'Ingeniería de Software I', 4],
            ['INF-501', 'Programación Web', 4],
            ['INF-502', 'Inteligencia Artificial', 4],
            ['INF-503', 'Redes de Computadoras II', 3],
            ['INF-504', 'Ingeniería de Software II', 4],
            ['ADM-301', 'Investigación Operativa', 3],
            ['INF-601', 'Desarrollo de Aplicaciones Móviles', 4],
            ['INF-602', 'Seguridad Informática', 4],
            ['INF-603', 'Sistemas Distribuidos', 3],
            ['ADM-401', 'Gestión de Proyectos', 3],
            ['ADM-402', 'Emprendimiento', 2],
        ];
        
        foreach ($subjects as $subject) {
            $id = DB::table('subjects')->insertGetId([
                'code' => $subject[0],
                'name' => $subject[1],
                'credits' => $subject[2],
                'description' => 'Materia: ' . $subject[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->subjectIds[] = $id;
        }
        
        echo "   ✓ 30 materias creadas\n";
    }
    
    private function createTeachers()
    {
        echo "👨‍🏫 Creando 30 docentes...\n";
        
        $nombres = [
            'Dr. Juan Pérez García', 'Dra. María López Silva', 'Ing. Carlos Rodríguez Díaz',
            'Lic. Ana Martínez Torres', 'Dr. Roberto Sánchez Ruiz', 'Ing. Laura Fernández Castro',
            'Dr. Miguel Torres Vargas', 'Lic. Patricia Gómez Morales', 'Ing. Jorge Ramírez Luna',
            'Dra. Carmen Flores Ortiz', 'Dr. Fernando Díaz Herrera', 'Lic. Isabel Castro Mendoza',
            'Ing. Ricardo Moreno Vega', 'Dra. Sofía Jiménez Ramos', 'Dr. Alberto Cruz Navarro',
            'Lic. Gabriela Reyes Campos', 'Ing. Daniel Ortega Silva', 'Dra. Verónica Guzmán Pérez',
            'Dr. Andrés Vargas Rojas', 'Lic. Mónica Herrera Santos', 'Ing. Pablo Mendoza García',
            'Dra. Claudia Romero Luna', 'Dr. Sergio Castro Díaz', 'Lic. Beatriz Soto Flores',
            'Ing. Raúl Guerrero Medina', 'Dr. Eduardo Silva Paredes', 'Dra. Lucía Navarro Campos',
            'Ing. Javier Morales Ruiz', 'Lic. Sandra Vega Torres', 'Dr. Héctor Ramos Ortiz'
        ];
        
        $departments = ['Sistemas', 'Redes', 'Industrial', 'Electrónica', 'Civil'];
        
        foreach ($nombres as $index => $nombre) {
            $email = 'docente' . ($index + 1) . '@ficct.edu.bo';
            
            $userId = DB::table('users')->insertGetId([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => 3, // DOCENTE
            ]);
            
            $teacherId = DB::table('teachers')->insertGetId([
                'user_id' => $userId,
                'name' => $nombre,
                'email' => $email,
                'dni' => '12345' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'phone' => '701234' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'department' => $departments[$index % 5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->teacherIds[] = $teacherId;
            $this->userIds[] = $userId;
        }
        
        echo "   ✓ 30 docentes creados\n";
    }
    
    private function createStudents()
    {
        echo "👨‍🎓 Creando 30 estudiantes...\n";
        
        $nombres = [
            'Alejandro', 'Beatriz', 'Carlos', 'Diana', 'Eduardo',
            'Fernanda', 'Gabriel', 'Helena', 'Ignacio', 'Julia',
            'Kevin', 'Laura', 'Manuel', 'Natalia', 'Oscar',
            'Patricia', 'Quintín', 'Rosa', 'Samuel', 'Teresa',
            'Ulises', 'Valeria', 'Walter', 'Ximena', 'Yolanda',
            'Zacarías', 'Andrea', 'Bruno', 'Carla', 'Diego'
        ];
        
        $apellidos = [
            'González', 'Rodríguez', 'Martínez', 'García', 'López',
            'Hernández', 'Pérez', 'Sánchez', 'Ramírez', 'Torres'
        ];
        
        foreach ($nombres as $index => $nombre) {
            $apellido = $apellidos[$index % 10];
            $nombreCompleto = "$nombre $apellido";
            $email = 'est' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '@ficct.edu.bo';
            
            $userId = DB::table('users')->insertGetId([
                'name' => $nombreCompleto,
                'email' => $email,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => 4, // ESTUDIANTE
            ]);
            
            $this->studentIds[] = $userId;
            $this->userIds[] = $userId;
        }
        
        echo "   ✓ 30 estudiantes creados\n";
    }

    
    private function createGroups()
    {
        echo "👥 Creando 30 grupos...\n";
        
        $letters = ['A', 'B', 'C', 'D', 'E'];
        
        for ($i = 0; $i < 30; $i++) {
            $subjectId = $this->subjectIds[$i % count($this->subjectIds)];
            $subject = DB::table('subjects')->where('id', $subjectId)->first();
            $letter = $letters[$i % 5];
            
            $id = DB::table('groups')->insertGetId([
                'code' => $subject->code . '-' . $letter,
                'name' => 'Grupo ' . $letter,
                'subject_id' => $subjectId,
                'capacity' => 25 + ($i % 15),
                'schedule' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->groupIds[] = $id;
        }
        
        echo "   ✓ 30 grupos creados\n";
    }
    
    private function createTeacherAssignments()
    {
        echo "📝 Creando 30 asignaciones de docentes...\n";
        
        for ($i = 0; $i < 30; $i++) {
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            $subjectId = $this->subjectIds[$i % count($this->subjectIds)];
            $groupId = $this->groupIds[$i % count($this->groupIds)];
            $periodId = $this->periodIds[count($this->periodIds) - 1]; // Periodo activo
            
            $id = DB::table('teacher_assignments')->insertGetId([
                'teacher_id' => $teacherId,
                'subject_id' => $subjectId,
                'group_id' => $groupId,
                'period_id' => $periodId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->assignmentIds[] = $id;
        }
        
        echo "   ✓ 30 asignaciones creadas\n";
    }
    
    private function createSchedules()
    {
        echo "⏰ Creando 30 horarios...\n";
        
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        
        for ($i = 0; $i < 30; $i++) {
            $groupId = $this->groupIds[$i % count($this->groupIds)];
            $roomId = $this->roomIds[$i % count($this->roomIds)];
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            
            $day = $days[$i % 5];
            $startHour = 7 + ($i % 6);
            $endHour = $startHour + 2;
            
            $id = DB::table('schedules')->insertGetId([
                'group_id' => $groupId,
                'room_id' => $roomId,
                'teacher_id' => $teacherId,
                'day_of_week' => $day,
                'start_time' => sprintf('%02d:00:00', $startHour),
                'end_time' => sprintf('%02d:00:00', $endHour),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->scheduleIds[] = $id;
        }
        
        echo "   ✓ 30 horarios creados\n";
    }
    
    private function createAttendances()
    {
        echo "✅ Creando 30 asistencias de docentes...\n";
        
        $statuses = ['present', 'absent', 'late'];
        
        for ($i = 0; $i < 30; $i++) {
            $scheduleId = $this->scheduleIds[$i % count($this->scheduleIds)];
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            
            $date = Carbon::now()->subDays(rand(1, 30));
            
            DB::table('attendances')->insert([
                'schedule_id' => $scheduleId,
                'teacher_id' => $teacherId,
                'date' => $date->format('Y-m-d'),
                'time' => $date->format('H:i:s'),
                'status' => $statuses[$i % 3],
                'notes' => $i % 3 == 0 ? 'Observación de asistencia ' . ($i + 1) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 asistencias creadas\n";
    }
    
    private function createClassCancellations()
    {
        echo "❌ Creando 30 anulaciones de clases...\n";
        
        $reasons = [
            'Feriado nacional',
            'Actividad académica especial',
            'Enfermedad del docente',
            'Mantenimiento del aula',
            'Reunión administrativa',
            'Capacitación docente',
            'Evento institucional',
            'Problema técnico',
        ];
        
        $modes = ['cancelled', 'virtual'];
        
        for ($i = 0; $i < 30; $i++) {
            $scheduleId = $this->scheduleIds[$i % count($this->scheduleIds)];
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            
            DB::table('class_cancellations')->insert([
                'schedule_id' => $scheduleId,
                'teacher_id' => $teacherId,
                'mode' => $modes[$i % 2],
                'reason' => $reasons[$i % count($reasons)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 anulaciones creadas\n";
    }
    
    private function createConflicts()
    {
        echo "⚠️  Creando 30 conflictos...\n";
        
        $types = ['teacher', 'room', 'time', 'capacity'];
        
        for ($i = 0; $i < 30; $i++) {
            $scheduleAId = $this->scheduleIds[$i % count($this->scheduleIds)];
            $scheduleBId = $this->scheduleIds[($i + 1) % count($this->scheduleIds)];
            
            DB::table('conflicts')->insert([
                'schedule_a_id' => $scheduleAId,
                'schedule_b_id' => $scheduleBId,
                'type' => $types[$i % 4],
                'resolved' => DB::raw(($i % 2 == 0) ? 'true' : 'false'),
                'resolution_note' => $i % 2 == 0 ? 'Conflicto resuelto - Caso ' . ($i + 1) : null,
                'resolved_at' => $i % 2 == 0 ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 conflictos creados\n";
    }
    
    private function createReservations()
    {
        echo "📅 Creando 30 reservas de aulas...\n";
        
        for ($i = 0; $i < 30; $i++) {
            $roomId = $this->roomIds[$i % count($this->roomIds)];
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            $scheduleId = $this->scheduleIds[$i % count($this->scheduleIds)];
            
            $reservedAt = Carbon::now()->addDays(rand(1, 30));
            $expiresAt = $reservedAt->copy()->addHours(2);
            
            DB::table('reservations')->insert([
                'room_id' => $roomId,
                'schedule_id' => $scheduleId,
                'teacher_id' => $teacherId,
                'reserved_at' => $reservedAt,
                'expires_at' => $expiresAt,
                'notes' => $i % 3 == 0 ? 'Reserva para actividad especial ' . ($i + 1) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 reservas creadas\n";
    }
    
    private function createAnnouncements()
    {
        echo "📢 Creando 30 anuncios...\n";
        
        $titles = [
            'Inicio de clases',
            'Exámenes finales',
            'Inscripciones abiertas',
            'Mantenimiento programado',
            'Evento académico',
            'Cambio de horario',
            'Suspensión de clases',
            'Reunión general',
            'Conferencia magistral',
            'Taller de capacitación',
        ];
        
        for ($i = 0; $i < 30; $i++) {
            $publishedBy = $this->userIds[$i % count($this->userIds)];
            
            DB::table('announcements')->insert([
                'title' => $titles[$i % count($titles)] . ' - ' . ($i + 1),
                'body' => 'Contenido del anuncio número ' . ($i + 1) . '. Este es un mensaje importante para toda la comunidad académica.',
                'published_by' => $publishedBy,
                'published_at' => Carbon::now()->subDays(rand(0, 30)),
                'expires_at' => Carbon::now()->addDays(rand(7, 60)),
                'pinned' => DB::raw(($i % 5 == 0) ? 'true' : 'false'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 anuncios creados\n";
    }
    
    private function createIncidents()
    {
        echo "🚨 Creando 30 incidencias...\n";
        
        $statuses = ['open', 'in_progress', 'resolved'];
        
        for ($i = 0; $i < 30; $i++) {
            $roomId = $this->roomIds[$i % count($this->roomIds)];
            $teacherId = $this->teacherIds[$i % count($this->teacherIds)];
            $reportedBy = $this->userIds[$i % count($this->userIds)];
            
            DB::table('incidents')->insert([
                'teacher_id' => $teacherId,
                'room_id' => $roomId,
                'description' => 'Incidencia número ' . ($i + 1) . ': Problema reportado que requiere atención.',
                'status' => $statuses[$i % 3],
                'reported_by' => $reportedBy,
                'resolved_by' => $i % 3 == 2 ? $this->userIds[0] : null,
                'resolved_at' => $i % 3 == 2 ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "   ✓ 30 incidencias creadas\n";
    }

    
    private function createActivityLogs()
    {
        echo "📋 Creando 50 registros de bitácora...\n";
        
        $actions = [
            'login', 'logout', 'create', 'update', 'delete', 
            'view', 'export', 'import', 'approve', 'reject'
        ];
        
        $modules = [
            'users', 'teachers', 'students', 'subjects', 'groups',
            'rooms', 'schedules', 'attendances', 'announcements', 'incidents'
        ];
        
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];
        
        for ($i = 0; $i < 50; $i++) {
            $userId = $this->userIds[$i % count($this->userIds)];
            $user = DB::table('users')->where('id', $userId)->first();
            
            $action = $actions[$i % count($actions)];
            $module = $modules[$i % count($modules)];
            
            DB::table('activity_logs')->insert([
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => 'ADMIN',
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'action' => $action,
                'module' => $module,
                'description' => ucfirst($action) . ' en módulo ' . $module . ' - Registro ' . ($i + 1),
                'url' => '/admin/' . $module,
                'method' => $methods[$i % 4],
                'old_values' => $action == 'update' ? json_encode(['field' => 'old_value']) : null,
                'new_values' => in_array($action, ['create', 'update']) ? json_encode(['field' => 'new_value']) : null,
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
        
        echo "   ✓ 50 registros de bitácora creados\n";
    }
    
    private function showSummary()
    {
        echo "\n📊 RESUMEN DE REGISTROS CREADOS:\n";
        echo "================================\n";
        
        $tables = [
            'roles' => 'Roles',
            'users' => 'Usuarios',
            'academic_periods' => 'Periodos Académicos',
            'rooms' => 'Aulas',
            'subjects' => 'Materias',
            'teachers' => 'Docentes',
            'groups' => 'Grupos',
            'teacher_assignments' => 'Asignaciones',
            'schedules' => 'Horarios',
            'attendances' => 'Asistencias',
            'class_cancellations' => 'Anulaciones',
            'conflicts' => 'Conflictos',
            'reservations' => 'Reservas',
            'announcements' => 'Anuncios',
            'incidents' => 'Incidencias',
            'activity_logs' => 'Bitácora',
        ];
        
        $total = 0;
        foreach ($tables as $table => $label) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $count = DB::table($table)->count();
                $total += $count;
                echo sprintf("%-30s: %d\n", $label, $count);
            }
        }
        
        echo "================================\n";
        echo sprintf("%-30s: %d\n", "TOTAL DE REGISTROS", $total);
        
        echo "\n🔑 CREDENCIALES DE ACCESO:\n";
        echo "================================\n";
        echo "Admin:      admin@ficct.edu.bo / password\n";
        echo "Docentes:   docente1@ficct.edu.bo hasta docente30@ficct.edu.bo / password\n";
        echo "Estudiantes: est001@ficct.edu.bo hasta est030@ficct.edu.bo / password\n";
        echo "\n✨ ¡Base de datos lista para usar!\n";
    }
}
