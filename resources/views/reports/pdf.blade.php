<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #881F34;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #881F34;
            margin: 0;
            font-size: 20px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #881F34;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #881F34;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>FICCT - Sistema de Gestión Académica</p>
        <p>Generado el: {{ $date }}</p>
    </div>

    @if($type === 'teacher-workload')
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Email</th>
                    <th>Horarios</th>
                    <th>Horas Totales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->teacher_name ?? '' }}</td>
                    <td>{{ $item->email ?? '' }}</td>
                    <td>{{ $item->total_schedules ?? 0 }}</td>
                    <td>{{ number_format($item->total_hours ?? 0, 2) }} hrs</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif


    @if($type === 'teacher-attendance')
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Total</th>
                    <th>Presentes</th>
                    <th>Ausentes</th>
                    <th>Tardanzas</th>
                    <th>% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->teacher_name ?? '' }}</td>
                    <td>{{ $item->total_records ?? 0 }}</td>
                    <td>{{ $item->present_count ?? 0 }}</td>
                    <td>{{ $item->absent_count ?? 0 }}</td>
                    <td>{{ $item->late_count ?? 0 }}</td>
                    <td>{{ $item->attendance_percentage ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'absences')
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Asistencias</th>
                    <th>Ausencias</th>
                    <th>Total Clases</th>
                    <th>% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->teacher_name ?? '' }}</td>
                    <td>{{ $item->attendances ?? 0 }}</td>
                    <td>{{ $item->absences ?? 0 }}</td>
                    <td>{{ $item->total_classes ?? 0 }}</td>
                    <td>{{ number_format($item->attendance_rate ?? 0, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'weekly-schedule')
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Horario</th>
                    <th>Aula</th>
                    <th>Docente</th>
                    <th>Grupo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->day ?? '' }}</td>
                    <td>{{ $item->start_time ?? '' }} - {{ $item->end_time ?? '' }}</td>
                    <td>{{ $item->room_name ?? '' }} ({{ $item->location ?? '' }})</td>
                    <td>{{ $item->teacher_name ?? '' }}</td>
                    <td>{{ $item->group_name ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'available-rooms')
        <table>
            <thead>
                <tr>
                    <th>Aula</th>
                    <th>Ubicación</th>
                    <th>Capacidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->name ?? '' }}</td>
                    <td>{{ $item->location ?? '' }}</td>
                    <td>{{ $item->capacity ?? 0 }} personas</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'subject-attendance')
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Código</th>
                    <th>Total Clases</th>
                    <th>Presentes</th>
                    <th>Ausentes</th>
                    <th>% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->subject_name ?? '' }}</td>
                    <td>{{ $item->subject_code ?? 'N/A' }}</td>
                    <td>{{ $item->total_classes ?? 0 }}</td>
                    <td>{{ $item->present_count ?? 0 }}</td>
                    <td>{{ $item->absent_count ?? 0 }}</td>
                    <td>{{ $item->attendance_percentage ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'group-attendance')
        <table>
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th>Días</th>
                    <th>Total Registros</th>
                    <th>Presentes</th>
                    <th>% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->group_name ?? '' }}</td>
                    <td>{{ $item->total_days ?? 0 }}</td>
                    <td>{{ $item->total_records ?? 0 }}</td>
                    <td>{{ $item->present_count ?? 0 }}</td>
                    <td>{{ $item->attendance_percentage ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'general-stats')
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Docentes</div>
                <div class="stat-value">{{ $data->total_teachers ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Estudiantes</div>
                <div class="stat-value">{{ $data->total_students ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Aulas</div>
                <div class="stat-value">{{ $data->total_rooms ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Materias</div>
                <div class="stat-value">{{ $data->total_subjects ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Grupos</div>
                <div class="stat-value">{{ $data->total_groups ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Asignaciones del Período</div>
                <div class="stat-value">{{ $data->period_assignments ?? 0 }}</div>
            </div>
        </div>
    @endif
</body>
</html>
