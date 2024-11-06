<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CUENTAS DE BANCO</title>
    <link rel="stylesheet" href="{{ public_path('css/custom_pdf.css') }}">    <!--estilos de hoja pdf-->
    <link rel="stylesheet" href="{{ public_path('css/custom_page.css') }}"> <!--estilos de hoja pdf-->
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
                    <span style="font-size: 20px; font-weigth: bold;">Movimientos Bancarios</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    @if ($reportRange == 0)
                        <span style="font-size: 16px"><strong>Movimientos del Dia</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span>
                    @else
                        <span style="font-size: 16px"><strong>Movimientos por Fecha</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{\Carbon\Carbon::parse($dateFrom)->format('d-m-Y')}} al {{\Carbon\Carbon::parse($dateTo)->format('d-m-Y')}}</strong></span>
                    @endif
                    <br>
                    <span style="font-size: 14px"><strong>Cuenta: {{ $account }}</strong></span>
                </td>
            </tr>
        </table>
    </header>
    <section>
        <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
            <thead>
                <tr>
                    <th width="40%">Descripcion</th>
                    <th width="15%">Saldo previo</th>
                    <th width="15%">Monto</th>
                    <th width="15%">Saldo Actual</th>
                    <th width="15%">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td align="center">{{ $detail->description }}</td>
                        <td align="center">${{ number_format($detail->previus_balance,2) }}</td>
                        <td align="center">${{ number_format($detail->amount,2) }}</td>
                        <td align="center">${{ number_format($detail->actual_balance,2) }}</td>
                        <td align="center">{{\Carbon\Carbon::parse($detail->created_at)->format('d-m-Y')}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>