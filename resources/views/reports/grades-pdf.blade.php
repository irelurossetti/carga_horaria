<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Calificaciones - {{ $group->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; color: #881F34; }
        .header p { margin: 3px 0; color: #666; font-size: 10px; }
        .info { margin-bottom: 15px; background-color: #f9f9f9; padding: 10px; border-radius: 5px; }
        .info table { width: 100%; }
        .info td { padding: 3px; font-size: 10px; }
        .info td:first-child { font-weight: bold; width: 120px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
        th { background-color: #881F34; color: white; padding: 8px 4px; text-align: center; font-size: 9px; }
        td { padding: 6px 4px; border-bottom: 1px solid #ddd; text-align: center; }
        td:first-child { text-align: left; }
        tr:hover { background-color: #f5f5f5; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        .status-pass { color: #059669; font-weight: bold; }
        .status-fail { color: #DC2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE CALIFICACIONES</h1>
        <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
        <p>Fecha: {{ $date }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Grupo:</td>
                <td>{{ $group->name }}</td>
                <td>Materia:</td>
                <td>{{ $group->subject ? $group->subject->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Total Estudiantes:</td>
                <td>{{ count($students) }}</td>
                <td>Promedio General:</td>
                <td><strong>{{ number_format($stats['average'], 2) }}</strong></td>
            </tr>
            <tr>
                <td>Aprobados:</td>
                <td class="status-pass">{{ $stats['passed'] }}</td>
                <td>Reprobados:</td>
                <td class="status-fail">{{ $stats['failed'] }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Estudiante</th>
                <th>Código</th>
                @foreach($criteria as $criterion)
                    <th>{{ $criterion->name }}<br><small>({{ $criterion->weight }}%)</small></th>
                @endforeach
                <th style="background-color: #1E40AF;">Nota Final</th>
                <th style="background-color: #1E40AF;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php
                    $finalGrade = $student->final_grade ?? 0;
                    $status = $finalGrade >= 51 ? 'Aprobado' : 'Reprobado';
                    $statusClass = $finalGrade >= 51 ? 'status-pass' : 'status-fail';
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $student->name }}</td>
                    <td>{{ $student->registration_number ?? 'N/A' }}</td>
                    @foreach($criteria as $criterion)
                        @php
                            $score = '-';
                            if (isset($student->grades)) {
                                $grades = is_array($student->grades) ? collect($student->grades) : $student->grades;
                                $grade = $grades->firstWhere('criteria_id', $criterion->id);
                                if ($grade) {
                                    $score = is_array($grade) ? ($grade['score'] ?? '-') : ($grade->score ?? '-');
                                }
                            }
                        @endphp
                        <td>{{ is_numeric($score) ? number_format($score, 2) : $score }}</td>
                    @endforeach
                    <td><strong>{{ number_format($finalGrade, 2) }}</strong></td>
                    <td class="{{ $statusClass }}">{{ $status }}</td>
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
