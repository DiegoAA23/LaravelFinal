<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calificaciones</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            margin: 0 auto;
            padding: 48px;
            max-width: 1200px; /* Aumentar el ancho máximo */
            margin-bottom: 2rem;
            margin-top: 2rem;
        }

        .grades-container {
            background-color: #ffffff;
            padding: 24px;
            border-radius: 8px;
            text-align: center;
            color: #2d3748;
            border: 1px solid #000000;
            margin-bottom: 2rem;
            margin-top: 2rem;
        }

        .student-info-card {
            background-color: rgb(207, 226, 215);
            color: black;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }

        .student-info-card h2 {
            font-size: 1.25em;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        table, th, td {
            border: 1px solid #e2e8f0;
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

        h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    @php $cont = 0; @endphp
    <div class="container">
        <div class="grades-container">
            <h2>Calificaciones del Estudiante</h2>
            @foreach ($item as $it)
            @if ($cont == 0)
            <div class="student-info-card">
                <h2>Información del Alumno</h2>
                <p><strong>Nombre: </strong>{{ Crypt::decryptString($it->nombre_estudiante) }} {{
                    Crypt::decryptString($it->apellido_estudiante) }}</p>
                <p><strong>Cuenta: </strong>{{ Crypt::decryptString($it->id_estudiante) }}</p>
                <p><strong>Carrera: </strong>Ingeniería en Ciencias de la Computación</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Clase</th>
                        <th>Profesor</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @endif
                    <tr>
                        <td>{{ Crypt::decryptString($it->nombre_clase) }}</td>
                        <td>{{ Crypt::decryptString($it->nombre_profesor) }} {{
                            Crypt::decryptString($it->apellido_profesor) }}</td>
                        <td>@if ($it->nota == 0.0) N.A. @else {{ $it->nota }} @endif</td>
                    </tr>
                    @php $cont++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
