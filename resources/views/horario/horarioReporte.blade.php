<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte - Horarios</title>
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
    <h2 class="header">
        H O R A R I O S
    </h2>
    <div class="table-container">
        <div class="table-wrapper">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Curso</th>
                        <th>Aula</th>
                        <th>Días</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($horarios as $horario)
                    <tr>
                        <td>{{
                            $horario->id_horario}}</td>
                        <td>{{
                            Crypt::decryptString($horario->clase->nombre_clase)}}</td>
                        <td>{{
                            $horario->aula->aula}}</td>
                        <td>{{
                            $horario->dias
                            }}</td>
                        <td>{{
                            $horario->fecha_inicio }}</td>
                        <td>{{
                            $horario->fecha_fin }}</td>
                        <td>{{
                            $horario->hora_inicio }}</td>
                        <td>{{
                            $horario->hora_fin }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>