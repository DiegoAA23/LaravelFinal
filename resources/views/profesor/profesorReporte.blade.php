<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte - Profesores</title>
    <style>
        html {
            font-family: 'Helvetica', sans-serif;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-top: 1.5rem;
            font-size: 1.25rem;
        }

        .table-container {
            padding: 2rem 0;
            max-width: 80rem;
            margin: 0 auto;
        }

        .table-wrapper {
            background-color: #ffffff;
            overflow-x: auto;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #007bff;
            color: white;
            padding: 0.75rem;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #ece9e9;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody td {
            padding: 0.75rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>P R O F E S O R E S</h1>
        <p class="date">{{ \Carbon\Carbon::now()->locale('es')->format('l, j F Y \- H:i') }}</p>
    </div>
    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Especialidad</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profesores as $profesore)
                    <tr>
                        <td>{{ $profesore->id_profesor }}</td>
                        <td>{{ $profesore->nombre }}</td>
                        <td>{{ $profesore->apellido }}</td>
                        <td>{{ $profesore->especialidad->especialidad }}</td>
                        <td>{{ $profesore->correo_electronico }}</td>
                        <td>{{ $profesore->telefono }}</td>
                        <td>{{ $profesore->estado->descripcion }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>