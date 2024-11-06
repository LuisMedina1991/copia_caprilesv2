<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Caja General</title>
    <link rel="stylesheet" href="{{ public_path('css/custom_pdf.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/custom_page.css') }}">
</head>
<body>
    <header>
        <table width="100%">
            <tr>
                <td class="text-center">
                    <span style="font-size: 25px; font-weigth: bold;">Importadora Capriles</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <span style="font-size: 20px; font-weigth: bold;">Caja General</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <span style="font-size: 16px"><strong>Total: ${{number_format($my_total,2)}}</strong></span>
                    <br>
                    @if ($reportRange == 0)
                        <span style="font-size: 16px"><strong>Reporte del Dia</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span>
                    @else
                        <span style="font-size: 16px"><strong>Reporte por Fecha</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} al {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</strong></span>
                    @endif
                </td>
            </tr>
        </table>
    </header>
    <section>
        <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
            <thead>
                <tr>
                    <th width="55%">DESCRIPCION</th>
                    <th width="10%">MOVIMIENTO</th>
                    <th width="10%">RELACION</th>
                    <th width="10%">MONTO</th>
                    @if ($reportRange != 0)
                    <th width="10%">FECHA</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $d)
                    <tr>
                        <td align="center">{{ $d->description }}</td>
                        <td align="center">{{ $d->action }}</td>
                        <td align="center">{{ $d->type }}</td>
                        <td align="center">${{number_format($d->amount,2)}}</td>
                        @if ($reportRange != 0)
                        <td align="center">{{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>