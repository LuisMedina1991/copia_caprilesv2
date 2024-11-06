<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title text-uppercase">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                <div class="container">
                    <div class="row row-cols-4">
                        <div class="col">
                            <h5>SALDO CAJA GENERAL: ${{ number_format(($my_total),2)}}</h5>
                        </div>
                        <div class="col">
                            <a href="javascript:void(0)" class="btn btn-dark btn-md {{$reportRange != 0 ? 'disabled' : ''}}" data-toggle="modal" data-target="#theModal">Nuevo Movimiento</a>
                        </div>
                        <div class="col">
                            <a href="javascript:void(0)" class="btn btn-dark btn-md {{$reportRange != 0 ? 'disabled' : ''}}" data-toggle="modal" data-target="#theModal2">Ingresar Utilidad</a>
                        </div>
                        <div class="col">
                            <a href="javascript:void(0)" class="btn btn-dark btn-md {{$reportRange != 0 ? 'disabled' : ''}}" onclick="Confirm2()">Obtener Ventas del Dia</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <h6><b>Elija una opcion</b></h6>
                    <div class="form-group">
                        <select wire:model="reportRange" class="form-control">
                            <option value="0">Registro del dia</option>
                            <option value="1">Registro por fecha</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <h6><b>Elija una opcion</b></h6>
                    <div class="form-group">
                        <select wire:model="reportType" class="form-control">
                            <option value="0">Reporte General</option>
                            @foreach ($transaction_types as $transaction_type)
                                <option value="{{$transaction_type['name']}}">{{$transaction_type['alias']}}</option>
                            @endforeach
                            {{--@foreach ($covers as $cover)
                                <option value="{{$cover->description}}">{{$cover->description}}</option>
                            @endforeach--}}
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <h6><b>Fecha Inicial</b></h6>
                    <div class="form-group">
                        <input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">
                    </div>
                </div>
                <div class="col-sm-2">
                    <h6><b>Fecha Final</b></h6>
                    <div class="form-group">
                        <input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">
                    </div>
                </div>
                <div class="col-sm-2">
                    <br>
                    <a href="{{ url('paydesk_report/pdf' . '/' . $reportRange . '/' . $reportType . '/' . $my_total . '/' . $dateFrom . '/' . $dateTo) }}" class="btn btn-dark btn-block {{count($details_2) < 1 ? 'disabled' : ''}}" target="_blank">
                        Generar PDF
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 text-center">
                    <h5><b>Total Ingresos: ${{ number_format(($i_total),2)}}</b></h5>
                </div>
                <div class="col-sm-6 text-center">
                    <h5><b>Total Egresos: ${{ number_format(($e_total),2)}}</b></h5>
                </div>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white text-center">DESCRIPCION</th>
                                <th class="table-th text-white text-center">MOVIMIENTO</th>
                                <th class="table-th text-white text-center">RELACION</th>
                                <th class="table-th text-white text-center">MONTO</th>
                                @if($reportRange == 0)
                                <th class="table-th text-white text-center">ACCIONES</th>
                                @else
                                <th class="table-th text-white text-center">FECHA</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details_2 as $d)
                            <tr>
                                <td><h6 class="text-center text-uppercase">{{$d->description}}</h6></td>   
                                <td><h6 class="text-center text-uppercase">{{$d->action}}</h6></td>
                                <td><h6 class="text-center text-uppercase">{{$d->type}}</h6></td>                        
                                <td><h6 class="text-center">${{number_format($d->amount,2)}}</h6></td>
                                <td class="text-center">
                                    @if($reportRange == 0)
                                    @can('eliminar_registro')
                                    <a href="javascript:void(0)" onclick="Confirm('{{$d->id}}')" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    @endcan
                                    @else
                                    <h6 class="text-center text-uppercase">{{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}</h6>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{--$paydesks->links()--}}
                </div>
            </div>
        </div>
    </div>
    @include('livewire.paydesk.form')
    @include('livewire.paydesk.form2')
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
        
        window.livewire.on('item-added', msg=>{  //evento al agregar registro
            $('#theModal').modal('hide')
            noty(msg)
        });

        window.livewire.on('item-deleted', msg=>{    //evento al eliminar registro
            noty(msg)
        });

        window.livewire.on('show-modal', msg=>{ //evento para mostral modal
            $('#theModal').modal('show')
        });

        window.livewire.on('modal-hide', msg=>{ //evento para cerrar modal
            $('#theModal').modal('hide')
        });

        $('#theModal').on('shown.bs.modal', function(e){    //metodo para autofocus al campo nombre
            $('.component-name').focus()
        });

        $('#theModal2').on('shown.bs.modal', function(e){    //metodo para autofocus al campo nombre
            $('.component-name').focus()
        });

        window.livewire.on('item-updated', msg=>{     //evento al actualizar registro
            $('#theModal2').modal('hide')
            noty(msg)
        });

        window.livewire.on('show-modal2', msg=>{     //evento para mostral modal
            $('#theModal2').modal('show')
        });

        window.livewire.on('paydesk-error', msg => {
            noty(msg,2)
        });

        window.livewire.on('movement-error', msg => {   //evento para los errores del componente
            noty(msg,2)
        });

        window.livewire.on('cover-error', msg=>{    //evento al eliminar registro
            noty(msg,2)
        });

        window.livewire.on('error-message', msg => {   //evento para mostrar mensajes de error
            noty(msg,2)
        });

    });

    function Confirm(id){

        swal({

            title: 'CONFIRMAR',
            text: '¿CONFIRMA ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'

        }).then(function(result){

            if(result.value){
                window.livewire.emit('destroy', id)
                swal.close()
            }
        })
    }

    function Confirm2(){

        swal({

            title: 'CONFIRMAR',
            text: 'SOLO SE PUEDE OBTENER LAS VENTAS UNA VEZ AL DIA ¿DESEA CONTINUAR?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'

        }).then(function(result){

            if(result.value){
                window.livewire.emit('collect', )
                swal.close()
            }
        })
    }

</script>