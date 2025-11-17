<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carga Horaria - {{ $teacher->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 5px; }
        .info td:first-child { font-weight: bold; width: 150px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #881F34; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
        .summary { background-color: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-label { font-weight: bold; color: #881F34; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE CARGA HORARIA</h1>
        <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
        <p>Fecha: {{ $date }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Docente:</td>
                <td>{{ $teacher->name }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $teacher->email }}</td>
            </tr>
            <tr>
                <td>Periodo:</td>
                <td>{{ $period }}</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total de Clases:</span> {{ $schedules->count() }}
        </div>
        <div class="summary-item">
            <span class="summary-label">Horas Semanales:</span> {{ $totalHours }}h
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Día</th>
                <th>Horario</th>
                <th>Materia</th>
                <th>Grupo</th>
                <th>Aula</th>
                <th>Horas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
            @php
                $start = $schedule->start_time ? substr($schedule->start_time, 0, 5) : '';
                $end = $schedule->end_time ? substr($schedule->end_time, 0, 5) : '';
                $hours = 0;
                if ($schedule->start_time && $schedule->end_time) {
                    $startTime = \Carbon\Carbon::parse($schedule->start_time);
                    $endTime = \Carbon\Carbon::parse($schedule->end_time);
                    $hours = $endTime->diffInHours($startTime);
                }
            @endphp
            <tr>
                <td>{{ $schedule->day_of_week ?? 'N/A' }}</td>
                <td>{{ $start }} - {{ $end }}</td>
                <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                <td>{{ $schedule->group->name ?? 'N/A' }}</td>
                <td>{{ $schedule->room->name ?? 'N/A' }}</td>
                <td>{{ $hours }}h</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión Académica - FICCT</p>
        <p>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
