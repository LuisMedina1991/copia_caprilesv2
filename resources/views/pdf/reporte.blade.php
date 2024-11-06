<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if ($reportType == 0)
     <title>Reporte General</title>
    @endif
    @if ($reportType == 1)
     <title>Reporte de Ingresos</title>
    @endif
    @if ($reportType == 2)
     <title>Reporte de Traspasos</title>
    @endif
    @if ($reportType == 3)
     <title>Reporte de Ventas</title>
    @endif
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
                    @if ($reportType == 0)
                    <span style="font-size: 20px; font-weigth: bold;">Reporte General</span>
                    @endif
                    @if ($reportType == 1)
                    <span style="font-size: 20px; font-weigth: bold;">Reporte de Ingresos</span>
                    @endif
                    @if ($reportType == 2)
                    <span style="font-size: 20px; font-weigth: bold;">Reporte de Traspasos</span>
                    @endif
                    @if ($reportType == 3)
                    <span style="font-size: 20px; font-weigth: bold;">Reporte de Ventas</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    @if ($reportRange == 0)
                        <span style="font-size: 16px"><strong>Reporte del Dia</strong></span>
                    @else
                        <span style="font-size: 16px"><strong>Reporte por Fecha</strong></span>
                    @endif
                    <br>
                    @if ($reportRange != 0)
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ $dateFrom }} al {{ $dateTo }}</strong></span>
                    @else
                        <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::today()->format('d-m-Y') }}</strong></span>
                    @endif
                </td>
            </tr>
        </table>
    </header>
    <section>

        @if ($reportType == 0)
            <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
                <thead>
                    <tr>
                        <th width="10%">TIPO</th>
                        <th width="10%">PRODUCTO</th>
                        <th width="10%">MARCA</th>
                        <th width="10%">COSTO</th>
                        <th width="10%">N° RECIBO</th>
                        <th width="10%">CANTIDAD</th>
                        <th width="10%">TOTAL</th>
                        <th width="10%">SUCURSAL</th>
                        {{--<th width="12%">ESTADO</th>--}}
                        <th>USUARIO</th>
                        <th width="10%">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($income as $i)
                        <tr>
                            <td align="center">INGRESO</td>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">{{ $i->office }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                    @foreach ($transfer as $i)
                        <tr>
                            <td align="center">TRASPASO</td>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">{{ $i->from_office }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                    @foreach ($sale as $i)
                        <tr>
                            <td align="center">VENTA</td>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">{{ $i->office }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTAL INGRESOS</b></span>
                        </td>
                        <td colspan="4"></td>
                        <td class="text-center">
                            <b>{{ $income->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($income->sum('total'), 2) }}</b></span>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTAL TRASPASOS</b></span>
                        </td>
                        <td colspan="4"></td>
                        <td class="text-center">
                            <b>{{ $transfer->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($transfer->sum('total'), 2) }}</b></span>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTAL VENTAS</b></span>
                        </td>
                        <td colspan="4"></td>
                        <td class="text-center">
                            <b>{{ $sale->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($sale->sum('total'), 2) }}</b></span>
                        </td>
                        {{--<td class="text-center">
                            <b>{{ $data->sum('utility') }}</b>
                        </td>--}}
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if ($reportType == 1)
            <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
                <thead>
                    <tr>
                        <th width="10%">PRODUCTO</th>
                        <th width="10%">MARCA</th>
                        <th width="10%">COSTO</th>
                        <th width="10%">N° RECIBO</th>
                        <th width="10%">CANTIDAD</th>
                        <th width="10%">TOTAL</th>
                        <th width="10%">SUCURSAL</th>
                        <th width="10%">TIPO DE INGRESO</th>
                        <th>USUARIO</th>
                        <th width="10%">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($income as $i)
                        <tr>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">{{ $i->office }}</td>
                            <td align="center">{{ $i->type }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTALES</b></span>
                        </td>
                        <td colspan="3"></td>
                        <td class="text-center">
                            <b>{{ $income->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($income->sum('total'), 2) }}</b></span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if ($reportType == 2)
            <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
                <thead>
                    <tr>
                        <th width="10%">PRODUCTO</th>
                        <th width="10%">MARCA</th>
                        <th width="10%">COSTO</th>
                        <th width="10%">N° RECIBO</th>
                        <th width="10%">CANTIDAD</th>
                        <th width="10%">TOTAL</th>
                        <th width="10%">ORIGEN</th>
                        <th width="10%">DESTINO</th>
                        <th>USUARIO</th>
                        <th width="10%">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer as $i)
                        <tr>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">{{ $i->from_office }}</td>
                            <td align="center">{{ $i->to_office }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTALES</b></span>
                        </td>
                        <td colspan="3"></td>
                        <td class="text-center">
                            <b>{{ $transfer->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($transfer->sum('total'), 2) }}</b></span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if ($reportType == 3)
            <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
                <thead>
                    <tr>
                        <th width="10%">PRODUCTO</th>
                        <th width="10%">MARCA</th>
                        <th width="10%">COSTO</th>
                        {{--<th width="10%">PRECIO</th>--}}
                        <th width="10%">PRECIO VENTA</th>
                        <th width="10%">CANTIDAD</th>
                        <th width="10%">TOTAL</th>
                        <th width="10%">UTILIDAD</th>
                        <th width="10%">SUCURSAL</th>
                        <th width="10%">N° RECIBO</th>
                        <th>USUARIO</th>
                        <th width="10%">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale as $i)
                        <tr>
                            <td align="center">{{ $i->code }}</td>
                            <td align="center">{{ $i->brand }}</td>
                            <td align="center">${{ number_format($i->cost, 2) }}</td>
                            {{--<td align="center">${{ $i->price }}</td>--}}
                            <td align="center">${{ number_format(($i->total / $i->quantity),2) }}</td>
                            <td align="center">{{ $i->quantity }}</td>
                            <td align="center">${{ number_format($i->total, 2) }}</td>
                            <td align="center">${{ number_format($i->utility, 2) }}</td>
                            <td align="center">{{ $i->office }}</td>
                            <td align="center">{{ $i->pf }}</td>
                            <td align="center">{{ $i->user }}</td>
                            <td align="center">{{\Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center">
                            <span><b>TOTALES</b></span>
                        </td>
                        <td colspan="3"></td>
                        <td class="text-center">
                            <b>{{ $sale->sum('quantity') }}</b>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($sale->sum('total'), 2) }}</b></span>
                        </td>
                        <td class="text-center">
                            <span><b>${{ number_format($sale->sum('utility'), 2) }}</b></span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
            </table>
        @endif
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