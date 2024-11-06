<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center"><b>{{$componentName}}</b></h4>
            </div>
            <div class="widget-content">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="col">
                            <div class="col-sm-12">
                                <h6>Seleccione la cuenta bancaria</h6>
                                <div class="form-group">
                                    <select wire:model="bank_account_id" class="form-control">
                                        <option value="0">Todas</option>
                                        @foreach ($bank_accounts as $bank_account)
                                            <option value="{{$bank_account->id}}">{{$bank_account->bank->description}} || {{$bank_account->type}} || {{$bank_account->currency}} || {{$bank_account->company->description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h6>Seleccione el alcance del reporte</h6>
                                <div class="form-group">
                                    <select wire:model="reportRange" class="form-control">
                                        <option value="0">Movimientos del dia</option>
                                        <option value="1">Movimientos por fecha</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha inicial</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="date_field_1" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha final</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="date_field_2" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                {{--<button wire:click="$refresh" class="btn btn-dark btn-block">
                                    Consultar
                                </button>--}}
                                <a href="{{ url('bank_account_report/pdf' . '/' . $bank_account_id . '/' . $reportRange . '/' . $date_field_1 . '/' . $date_field_2) }}" 
                                class="btn btn-dark btn-block {{count($details) < 1 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mt-1">
                                <thead class="text-white" style="background: #3B3F5C">
                                    <tr>
                                        <th class="table-th text-white text-center">DESCRIPCION</th>
                                        <th class="table-th text-white text-center">SALDO PREVIO</th>
                                        <th class="table-th text-white text-center">MONTO</th>
                                        <th class="table-th text-white text-center">SALDO ACTUAL</th>
                                        <th class="table-th text-white text-center">FECHA</th>
                                        @if($reportRange == 0)
                                        <th class="table-th text-white text-center">ACCIONES</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @if(count($details) < 1)
                                        <tr>
                                            <td colspan="10">
                                                <h6 class="text-center text-muted">Sin resultados</h6>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($details as $detail)           
                                    <tr>
                                        <td class="text-center"><h6>{{ $detail->description }}</h6></td>
                                        <td class="text-center"><h6>${{ number_format($detail->previus_balance,2) }}</h6></td>
                                        <td class="text-center"><h6>${{ number_format($detail->amount,2) }}</h6></td>
                                        <td class="text-center"><h6>${{ number_format($detail->actual_balance,2) }}</h6></td>
                                        <td class="text-center"><h6>{{\Carbon\Carbon::parse($detail->created_at)->format('d-m-Y')}}</h6></td>
                                        @if($reportRange == 0)
                                        @can('cancel_movement')
                                        <td class="text-center">
                                            <a href="javascript:void(0)" onclick="Confirm('{{$detail->id}}')" class="btn btn-dark" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                        @endcan
                                        @endif
                                    </tr>           
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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


        window.livewire.on('report-error', Msg => {   //evento para los errores del componente
            noty(Msg,2)
        });

    })

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

</script>