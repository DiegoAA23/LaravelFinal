<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historial de Clases</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-content: center;
            text-align: center;
        }

        .container {
            width: 100%;
            height: 70%;
            box-sizing: border-box;
            padding: 2rem;
        }

        .student-info-card {
            background-color: rgb(214, 230, 220);
            color: rgb(0, 0, 0);
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #000000;
        }

        .student-info-card h2 {
            font-size: 1.25em;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table, th, td, tr {
            border: 1px solid #000000;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: rgb(204, 231, 216);
        }

        td {
            background-color: #ffffff;
        }

        .grades-container {
            padding: 2rem;
            border: 1px solid #000000;
            margin-right: 5rem;
        }

        .grades-container h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .no-grades {
            text-align: center;
            font-size: 1.25em;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Historial de Clases</h1>
        <p class="date">{{ \Carbon\Carbon::now()->format('l, j F Y') }}</p>
    </div>
    <div class="student-info-card">
        <h2>Información del Alumno</h2>
        <p><strong>Nombre: </strong>{{ Crypt::decryptString($nombre_estudiante) }} {{
            Crypt::decryptString($apellido_estudiante) }}</p>
        <p><strong>Cuenta: </strong>{{ Crypt::decryptString($id_estudiante) }}</p>
        <p><strong>Carrera: </strong>Ingeniería en Ciencias de la Computación</p>
    </div>
    <div class="container">
        @if($item->isEmpty())
        <p class="no-grades">No ha cursado ningún periodo.</p>
        <p class="no-grades">Inscríbase en la pestaña de Matrícula.</p>
        @else
        @php
        $tmpPer = 0;
        @endphp
        <div class="grades-container">
            <h2>Historial de Clases</h2>
            <table>
                <thead>
                    <tr>
                        <th>Clase</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item as $it)
                    @if ($it->periodo !== $tmpPer)
                    @php
                    $tmpPer = $it->periodo;
                    @endphp
                    <tr>
                        <td colspan="4"><strong>Periodo {{ $tmpPer }}</strong></td>
                    @endif
                    <tr>
                        <td>{{ Crypt::decryptString($it->nombre_clase) }}</td>

                        <td>@if ($it->nota == 0.0) N.A. @else {{ $it->nota }} @endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</body>

</html>
