<div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">
                    <b>Detalle del Registro</b>
                </h5>
                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
            </div>
            <div class="modal-body">
                <div class="table-responsive">  <!--tabla-->
                    <table class="table table-bordered table-striped mt-1">
                        <thead class="text-white" style="background: #3B3F5C">  <!--encabezado de tabla-->
                            <tr>
                                <th class="table-th text-white text-center">MARCA</th>
                                <th class="table-th text-white text-center">MEDIDA</th>
                                <th class="table-th text-white text-center">PRECIO/COSTO</th>
                                <th class="table-th text-white text-center">CANT</th>
                                <th class="table-th text-white text-center">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody> <!--cuerpo de tabla-->
                            @foreach ($details as $detail)  <!--iteracion de los datos almacenados en variable pasada desde controlador-->
                            <tr>
                                <td class="text-center"><h6>{{$detail->brand}}</h6></td>
                                <td class="text-center"><h6>{{$detail->measurement}}</h6></td>
                                <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                                <td class="text-center"><h6>${{number_format($detail->price, 2)}}</h6></td>
                                <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                                <td class="text-center"><h6>{{number_format($detail->quantity, 0)}}</h6></td>
                                <!--aqui se obtiene la cantidad * precio y se da formato decimal al resultado-->
                                <td class="text-center"><h6>${{number_format($detail->price * $detail->quantity, 2)}}</h6></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><h5 class="text-center text-info font-weigth-bold">TOTALES</h5></td>
                                <td><h5 class="text-center text-info">{{$countDetails}}</h5></td>
                                <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                                <td><h5 class="text-center text-info">${{number_format($sumDetails, 2)}}</h5></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark close-btn text-white" data-dismiss="modal" style="background: #3B3F5C">CERRAR</button>
            </div>
        </div>
    </div>
</div>