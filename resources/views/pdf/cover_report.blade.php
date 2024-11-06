<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Caratula</title>
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
                    <span style="font-size: 20px; font-weigth: bold;">Caratula</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    @if ($reportRange == 0)
                        <span style="font-size: 16px"><strong>Reporte del Dia</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span>
                    @else
                        <span style="font-size: 16px"><strong>Reporte por Fecha</strong></span>
                        <br>
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{\Carbon\Carbon::parse($date)->format('d-m-Y')}}</strong></span>
                    @endif
                    <br>
                    <span style="font-size: 16px"><strong>Balance del Dia: {{number_format($sum10,2)}}</strong></span>
                </td>
            </tr>
        </table>
    </header>
    <section>
        <table class="table-items" width="100%">
            <thead>
                <tr>
                    <th width="40%">Descripcion</th>
                    <th width="15%">Saldo Dia Anterior</th>
                    <th width="15%">Ingreso</th>
                    <th width="15%">Egreso</th>
                    <th width="15%">Saldo del Dia</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach ($details as $detail)
                    @if($detail->type == 'balance_mensual' || $detail->type == 'mercaderia' || $detail->type == 'efectivo' || $detail->type == 'creditos' || $detail->type == 'depositos')
                        <tr>
                            @if($detail->cover)
                                <td align="center">{{ $detail->cover->description }}</td>
                            @else
                                <td align="center">cuenta eliminada</td>
                            @endif
                            <td align="center">${{ number_format($detail->previus_day_balance,2) }}</td>
                            <td align="center">${{ number_format($detail->ingress,2) }}</td>
                            <td align="center">${{ number_format($detail->egress,2) }}</td>
                            <td align="center">${{ number_format($detail->actual_balance,2) }}</td>
                        </tr>
                    @endif
                @endforeach

                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">SUMA INGRESOS: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum1,2)}}</h6></td>
                </tr>
                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">SUMA EGRESOS: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum2,2)}}</h6></td>
                </tr>
                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">TOTAL DE ACTIVOS I+E: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum3,2)}}</h6></td>
                </tr>

                @foreach ($details as $detail)
                    @if($detail->type == 'por_pagar')     
                        <tr>
                            <td align="center">{{ $detail->cover->description }}</td>
                            <td align="center">${{ number_format($detail->previus_day_balance,2) }}</h6></td>
                            <td align="center">${{ number_format($detail->ingress,2) }}</td>
                            <td align="center">${{ number_format($detail->egress,2) }}</td>
                            <td align="center">${{ number_format($detail->actual_balance,2) }}</td>
                        </tr>
                    @endif
                @endforeach

                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">TOTAL DEUDA: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum4,2)}}</h6></td>
                </tr>
                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">CAPITAL DE TRABAJO DEL DIA: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum5,2)}}</h6></td>
                </tr>

                @foreach ($details as $detail)
                    @if($detail->type == 'utilidad_diaria' || $detail->type == 'gasto_diario')     
                        <tr>
                            <td align="center">{{ $detail->cover->description }}</td>
                            <td align="center">${{ number_format($detail->previus_day_balance,2) }}</td>
                            <td align="center">${{ number_format($detail->ingress,2) }}</td>
                            <td align="center">${{ number_format($detail->egress,2) }}</td>
                            <td align="center">${{ number_format($detail->actual_balance,2) }}</td>
                        </tr>
                    @endif
                @endforeach

                <tr style="background: rgb(106, 168, 106)!important">
                    <td colspan="4"><h6 class="text-center">UTILIDAD NETA DEL DIA: </h6></td>
                    <td colspan="4"><h6 class="text-center">{{number_format($sum8,2)}}</h6></td>
                </tr>

                @foreach ($details as $detail)
                    @if($detail->type == 'facturas_mensual')
                        <tr style="background: rgb(106, 168, 106)!important">
                            <td align="center">{{ $detail->cover->description }}</td>
                            <td align="center">${{ number_format($detail->previus_day_balance,2) }}</td>
                            <td align="center">${{ number_format($detail->ingress,2) }}</td>
                            <td align="center">${{ number_format($detail->egress,2) }}</td>
                            <td align="center">${{ number_format($detail->actual_balance,2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </section>
    {{--<section class="footer">
        <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
            <tr>
                <td width="20%">
                    <span>IMPORTADORA CAPRILES</span>
                </td>
                <td width="60%" class="text-center">
                    importadoracapriles.com.bo
                </td>
                <td class="text-center" width="20%">
                    Pagina <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </section>--}}
</body>
</html>