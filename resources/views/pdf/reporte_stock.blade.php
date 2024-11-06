<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INVENTARIO</title>
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
                    <span style="font-size: 20px; font-weigth: bold;">Inventario</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <span style="font-size: 16px"><strong>VALOR TOTAL DE INVENTARIO: ${{ number_format($my_total,2) }}</strong></span>
                    <br>
                    <span style="font-size: 16px"><strong>Fecha de Consulta: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span>
                </td>
            </tr>
        </table>
    </header>
    <section>
        <table cellpadding="0" cellspacing="0" class="table-items" width="100%">
            <thead>
                <tr>
                    <th width="10%">Codigo</th>
                    <th width="30%">Descripcion</th>
                    <th width="10%">Costo</th>
                    <th width="10%">Precio</th>
                    @foreach($offices as $office)
                    <th>{{$office->name}}</th>
                    @endforeach
                    <th width="10%">Totales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        @if($stock->offices->sum('pivot.stock') > 0)
                            <td align="center">{{$stock->code}}</td>
                            <td align="center">{{$stock->brand}} | {{$stock->threshing}} | {{$stock->tarp}}</td>
                            <td align="center">${{number_format($stock->cost,2)}}</td>
                            <td align="center">${{number_format($stock->price,2)}}</td>
                            @foreach($stock->offices as $var)
                            <td align="center">{{$var->pivot->stock}}</td>
                            @endforeach
                            <td align="center">{{$stock->offices->sum('pivot.stock')}}</td>
                        @endif
                    </tr>
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