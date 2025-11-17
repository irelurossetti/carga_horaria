<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; color: #881F34; }
        .header p { margin: 5px 0; color: #666; }
        .stats { margin-bottom: 20px; background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
        .stats table { width: 100%; }
        .stats td { padding: 8px; }
        .stats td:first-child { font-weight: bold; color: #881F34; width: 200px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #881F34; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:hover { background-color: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</p>
        <p>Fecha: {{ $date }} | Periodo: {{ $period }}</p>
    </div>

    <div class="stats">
        <table>
            <tr>
                <td>Horas Semanales:</td>
                <td>{{ $stats->hours_week }}h</td>
            </tr>
            <tr>
                <td>Horas del Periodo:</td>
                <td>{{ $stats->hours_period }}h</td>
            </tr>
            <tr>
                <td>Total de Clases:</td>
                <td>{{ $stats->total_classes }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                @if($type === 'teacher')
                    <th>Docente</th>
                    <th>Email</th>
                    <th style="text-align: center;">Horarios</th>
                    <th style="text-align: center;">Horas</th>
                @elseif($type === 'subject')
                    <th>Materia</th>
                    <th>Código</th>
                    <th style="text-align: center;">Grupos</th>
                    <th style="text-align: center;">Horas</th>
                @elseif($type === 'group')
                    <th>Grupo</th>
                    <th>Materia</th>
                    <th style="text-align: center;">Horarios</th>
                    <th style="text-align: center;">Horas</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
                <tr>
                    @if($type === 'teacher')
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->schedules }}</td>
                        <td style="text-align: center;"><strong>{{ $item->hours }}h</strong></td>
                    @elseif($type === 'subject')
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->code ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->groups }}</td>
                        <td style="text-align: center;"><strong>{{ $item->hours }}h</strong></td>
                    @elseif($type === 'group')
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->subject ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->schedules }}</td>
                        <td style="text-align: center;"><strong>{{ $item->hours }}h</strong></td>
                    @endif
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
