<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center text-uppercase"><b>{{$componentName}}</b></h4>
                @if(count($details) > 1)
                    <b class="text-uppercase">Balance Diario: {{number_format($sum10,2)}}</b>
                @endif
            </div>
            <div class="widget-content">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="col">
                            @can('crear_caratula')
                            <div class="col-sm-12">
                                {{--<a href="javascript:void(0)" wire:click.prevent="CreateCover()" class="btn btn-dark btn-block {{$reportRange != 0 || count($details) > 0 ? 'disabled' : ''}}">Crear Caratula del Dia</a>--}}
                                <a href="javascript:void(0)" onclick="Message(1)" 
                                class="btn btn-dark btn-block {{$reportRange != 0 || count($details) > 0 ? 'disabled' : ''}}"
                                title="Crear nueva caratula para fecha actual">Crear Caratula</a>
                            </div>
                            <br>
                            @endcan
                            <div class="col-sm-12">
                                <h6>Opciones de Pagina</h6>
                                <div class="form-group">
                                    <select wire:model="reportRange" class="form-control">
                                        <option value="0">Caratula del Dia</option>
                                        <option value="1">Caratula por Fecha</option>
                                        <option value="2">Cambiar Fecha</option>
                                        <option value="3">Cerrar Mes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2 {{$reportRange == 0 || ($reportRange == 3 && $end_month_option != 1) ? 'invisible' : ''}}">
                                @switch($reportRange)
                                    @case(2)
                                        <h6>Fecha a Modificar</h6>
                                    @break
                                    @case(3)
                                        <h6>Nueva Fecha a Asignar</h6>
                                    @break
                                    @default
                                        <h6>Fecha de Caratula</h6>
                                @endswitch
                                <div class="form-group">
                                    <input type="text" wire:model="date_field_1" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2 {{$reportRange != 2 ? 'invisible' : ''}}">
                                <h6>Nueva Fecha a Asignar</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="date_field_2" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            @if($reportRange == 3)
                            <div class="col-sm-12">
                                <h6>Asignar Nueva Fecha?</h6>
                                <div class="form-group">
                                    <select wire:model="end_month_option" class="form-control">
                                        <option value="Elegir">Elegir</option>
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    @error('end_month_option')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            @if($uti_det)
                            @if($uti_det->actual_balance == $uti_det->previus_day_balance)
                            @can('ingresar_utilidad')
                            <div class="col-sm-12">
                                {{--<a href="javascript:void(0)" wire:click.prevent="EnterUtility()" class="btn btn-dark btn-block {{$reportRange != 0 || count($details) < 1 || $sum8 == 0 || $uti_det->actual_balance != $uti_det->previus_day_balance ? 'disabled' : ''}}">Ingresar Utilidad Acumulada</a>--}}
                                <a href="javascript:void(0)" onclick="Message(2)" 
                                class="btn btn-dark btn-block {{$reportRange != 0 || count($details) < 1 || $sum8 == 0 || $uti_det->actual_balance != $uti_det->previus_day_balance ? 'disabled' : ''}}"
                                title="Ingresar utilidad neta del dia">Ingresar Utilidad</a>
                            </div>
                            @endcan
                            @else
                            @can('revertir_utilidad')
                            <div class="col-sm-12">
                                {{--<a href="javascript:void(0)" wire:click.prevent="ReverseUtility()" class="btn btn-dark btn-block {{$reportRange != 0 || count($details) < 1 || $uti_det->actual_balance == $uti_det->previus_day_balance ? 'disabled' : ''}}">Revertir Utilidad Acumulada</a>--}}
                                <a href="javascript:void(0)" onclick="Message(3)" 
                                class="btn btn-dark btn-block {{$reportRange != 0 || count($details) < 1 || $uti_det->actual_balance == $uti_det->previus_day_balance ? 'disabled' : ''}}"
                                title="Revertir el ingreso de utilidad neta del dia">Revertir Utilidad</a>
                            </div>
                            @endcan
                            @endif
                            @endif
                            <br>
                            @can('cambiar_fecha_caratula')
                            <div class="col-sm-12">
                                {{--<a href="javascript:void(0)" wire:click.prevent="ChangeCoverDate()" class="btn btn-dark btn-block {{$reportRange != 2 || count($details) < 1 || $date_field_1 == '' || $date_field_2 == '' || $date_field_1 == $date_field_2 ? 'disabled' : ''}}">Cambiar Fecha</a>--}}
                                <a href="javascript:void(0)" onclick="Message(4)" 
                                class="btn btn-dark btn-block {{$reportRange != 2 || count($details) < 1 || $date_field_1 == '' || $date_field_2 == '' || $date_field_1 == $date_field_2 ? 'disabled' : ''}}" 
                                title="Asignar nueva fecha a la caratula">Cambiar Fecha</a>
                            </div>
                            <br>
                            @endcan
                            @if($uti_det)
                            @can('cerrar_mes')
                            <div class="col-sm-12">
                                {{--<a href="javascript:void(0)" wire:click.prevent="Close()" class="btn btn-dark btn-block {{$reportRange != 0 || count($details) < 1 ? 'disabled' : ''}}">Cerrar Mes</a>--}}
                                <a href="javascript:void(0)" onclick="Message(5)" 
                                class="btn btn-dark btn-block {{$reportRange != 3 || count($details) < 1 || $end_month_option == 'Elegir' || $uti_det->actual_balance == $uti_det->previus_day_balance ? 'disabled' : ''}}"
                                title="Iniciar nuevo balance mensual">Cerrar Mes</a>
                            </div>
                            <br>
                            @endcan
                            @endif
                            <div class="col-sm-12">
                                <a href="{{ url('cover_report/pdf' . '/' . $reportRange . '/' . $date_field_1) }}" 
                                class="btn btn-dark btn-block {{count($details) < 1 || $reportRange == 2 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9">
                        <div class="table-responsive">
                            <table class="table table-striped mt-1">
                                <thead class="text-white" style="background: #3B3F5C">
                                    <tr>
                                        <th class="table-th text-white text-center">DESCRIPCION</th>
                                        <th class="table-th text-white text-center">SALDO DIA ANTERIOR</th>
                                        <th class="table-th text-white text-center">INGRESO</th>
                                        <th class="table-th text-white text-center">EGRESO</th>
                                        <th class="table-th text-white text-center">SALDO DEL DIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @if(count($details) < 1)
                                        <tr>
                                            <td colspan="10">
                                                <h6 class="text-center text-muted">Sin resultados</h6>
                                            </td>
                                        </tr>
                                    @else

                                    @foreach ($details as $detail)
                                        @if($detail->type == 'balance_mensual' || $detail->type == 'mercaderia' || $detail->type == 'efectivo' || $detail->type == 'creditos' || $detail->type == 'depositos')
                                            <tr>
                                                @if($detail->cover)
                                                    <td class="text-center text-uppercase"><h6>{{ $detail->cover->description }}</h6></td>
                                                @else
                                                    <td class="text-center text-uppercase"><h6>cuenta eliminada</h6></td>
                                                @endif
                                                <td class="text-center"><h6>${{ number_format($detail->previus_day_balance,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->ingress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->egress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->actual_balance,2) }}</h6></td>
                                            </tr>
                                        @endif      
                                    @endforeach

                                    <tr  style="background: rgb(106, 168, 106)!important">
                                        <td colspan="4"><h6 class="text-center">SUMA INGRESOS: </h6></td>
                                        <td colspan="4"><h6 class="text-center">{{number_format($sum1,2)}}</h6></td>
                                    </tr>
                                    <tr   style="background: rgb(106, 168, 106)!important">
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
                                                <td class="text-center text-uppercase"><h6>{{ $detail->cover->description }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->previus_day_balance,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->ingress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->egress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->actual_balance,2) }}</h6></td>
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
                                                <td class="text-center text-uppercase"><h6>{{ $detail->cover->description }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->previus_day_balance,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->ingress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->egress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->actual_balance,2) }}</h6></td>
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
                                                <td class="text-center text-uppercase"><h6>{{ $detail->cover->description }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->previus_day_balance,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->ingress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->egress,2) }}</h6></td>
                                                <td class="text-center"><h6>${{ number_format($detail->actual_balance,2) }}</h6></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.cover_report.form')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        flatpickr(document.getElementsByClassName('flatpickr'), {   //evento para calendario personalizado
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },
            }
        })

        window.livewire.on('report-error', Msg => {
            noty(Msg)
        });

        window.livewire.on('cover-error', Msg => {
            noty(Msg,2)
        });

        window.livewire.on('item-added', Msg => {
            noty(Msg)
        });

        window.livewire.on('item-updated', msg=>{
            $('#theModal2').modal('hide')
            noty(msg)
        });

    })

    function Message(option)
    {
        swal({

            title: 'ADVERTENCIA',
            text: '¿CONFIRMA LA ACCION?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'

        }).then(function(result){

            if(result.value){

                if (option == 1) {

                    window.livewire.emit('CreateCover')

                }

                if (option == 2) {

                    window.livewire.emit('EnterUtility')

                }

                if (option == 3) {

                    window.livewire.emit('ReverseUtility')

                }

                if (option == 4) {

                    window.livewire.emit('ChangeCoverDate')

                }

                if (option == 5) {

                    window.livewire.emit('CloseMonth')

                }

                swal.close()
            }
        })
    }

</script>