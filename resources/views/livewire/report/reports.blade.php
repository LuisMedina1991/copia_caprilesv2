<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center"><b>{{$componentName}}</b></h4>
                @switch($reportType)
                    @case(1)
                        <h5>TOTAL DE INGRESOS: ${{number_format($income->sum('total'), 2)}}</h5>
                    @break
                    @case(3)
                    <h5>TOTAL DE VENTAS: ${{number_format($sale->sum('total'), 2)}}</h5>
                    <h5>TOTAL DE UTILIDAD: ${{number_format($sale->sum('utility'), 2)}}</h5>
                    @break   
                @endswitch
            </div>
            <div class="widget-content">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="col">
                            <div class="col-sm-12">
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-gp">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" wire:model="search" placeholder="Buscar medida..." class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h6>Elige el usuario</h6>
                                <div class="form-group">
                                    <select wire:model="userId" class="form-control">
                                        <option value="0">Todos</option>
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h6>Elige el tipo de reporte</h6>
                                <div class="form-group">
                                    <select wire:model="reportType" class="form-control">
                                        <option value="0">Reporte General</option>
                                        <option value="1">Reporte de Ingresos</option>
                                        <option value="2">Reporte de Traspasos</option>
                                        <option value="3">Reporte de Ventas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h6>Elige el alcance del reporte</h6>
                                <div class="form-group">
                                    <select wire:model="reportRange" class="form-control">
                                        <option value="0">Reportes del dia</option>
                                        <option value="1">Reportes por fecha</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha inicial</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha final</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button wire:click="$refresh" class="btn btn-dark btn-block">
                                    Consultar
                                </button>
                                @if($reportType == 0)
                                <a href="{{ url('report/pdf' . '/' . $userId . '/' . $reportRange . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo) }}" 
                                class="btn btn-dark btn-block {{(count($income) + count($transfer) + count($sale)) < 1 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                                @endif
                                @if($reportType == 1)
                                <a href="{{ url('report/pdf' . '/' . $userId . '/' . $reportRange . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo) }}" 
                                class="btn btn-dark btn-block {{count($income) < 1 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                                @endif
                                @if($reportType == 2)
                                <a href="{{ url('report/pdf' . '/' . $userId . '/' . $reportRange . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo) }}" 
                                class="btn btn-dark btn-block {{count($transfer) < 1 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                                @endif
                                @if($reportType == 3)
                                <a href="{{ url('report/pdf' . '/' . $userId . '/' . $reportRange . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo) }}" 
                                class="btn btn-dark btn-block {{count($sale) < 1 ? 'disabled' : ''}}" target="_blank">
                                    Generar PDF
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($reportType == 0)
                        <div class="col-sm-12 col-md-9">
                            <div class="table-responsive-xxl">
                                <table class="table table-sm table-striped table-bordered mt-1">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white text-center">TIPO</th>
                                            <th class="table-th text-white text-center">PRODUCTO</th>
                                            <th class="table-th text-white text-center">MARCA</th>
                                            <th class="table-th text-white text-center">COSTO</th>
                                            <th class="table-th text-white text-center">N° RECIBO</th>
                                            <th class="table-th text-white text-center">CANT</th>
                                            <th class="table-th text-white text-center">TOTAL</th>
                                            <th class="table-th text-white text-center">SUCURSAL</th>
                                            <th class="table-th text-white text-center">USUARIO</th>
                                            <th class="table-th text-white text-center">FECHA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($reportRange == 1 && ($dateFrom == '' || $dateTo == ''))
                                            <tr>
                                                <td colspan="10">
                                                    <h6 class="text-center text-muted">Sin resultados</h6>
                                                </td>
                                            </tr>
                                        @else
                                        @foreach ($income as $i)          
                                        <tr>
                                            <td class="text-center"><h6>INGRESO</h6></td>
                                            <td class="text-center"><h6>{{ $i->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->product->brand }} | {{$i->product->threshing}} | {{$i->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($i->product->cost,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($i->total,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->office }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</h6></td>
                                        </tr>           
                                        @endforeach
                                        @foreach ($transfer as $t)
                                        <tr>
                                            <td class="text-center"><h6>TRASPASO</h6></td>
                                            <td class="text-center"><h6>{{ $t->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->product->brand }} | {{$t->product->threshing}} | {{$t->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($t->product->cost,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($t->total,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->from_office }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($t->created_at)->format('d-m-Y')}}</h6></td>
                                        </tr>
                                        @endforeach
                                        @foreach ($sale as $s)
                                        <tr>
                                            <td class="text-center"><h6>VENTA</h6></td>
                                            <td class="text-center"><h6>{{ $s->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->product->brand }} | {{$s->product->threshing}} | {{$s->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($s->product->cost,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($s->total,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->office }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($s->created_at)->format('d-m-Y')}}</h6></td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($reportType == 1)
                        <div class="col-sm-12 col-md-9">
                            <div class="table-responsive-xxl">
                                <table class="table table-sm table-striped table-bordered mt-1">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white text-center">PRODUCTO</th>
                                            <th class="table-th text-white text-center">MARCA</th>
                                            <th class="table-th text-white text-center">COSTO</th>
                                            <th class="table-th text-white text-center">N° RECIBO</th>
                                            <th class="table-th text-white text-center">CANT</th>
                                            <th class="table-th text-white text-center">TOTAL</th>
                                            <th class="table-th text-white text-center">SUCURSAL</th>
                                            <th class="table-th text-white text-center">TIPO DE INGRESO</th>
                                            <th class="table-th text-white text-center">USUARIO</th>
                                            <th class="table-th text-white text-center">FECHA</th>
                                            <th class="table-th text-white text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @if($reportRange == 1 && ($dateFrom == '' || $dateTo == ''))
                                            <tr>
                                                <td colspan="10">
                                                    <h6 class="text-center text-muted">Sin resultados</h6>
                                                </td>
                                            </tr>
                                        @else

                                        @foreach ($income as $i)
            
                                        <tr>
                                            <td class="text-center"><h6>{{ $i->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->product->brand }} | {{$i->product->threshing}} | {{$i->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($i->product->cost,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($i->total,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->office }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->type }}</h6></td>
                                            <td class="text-center"><h6>{{ $i->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($i->created_at)->format('d-m-Y')}}</h6></td>
                                            @if(Carbon\Carbon::parse($i->created_at)->format('d-m-Y') == Carbon\Carbon::today()->format('d-m-Y'))
                                            @can('cancelar_ingreso')
                                            <td class="text-center">
                                                <a href="javascript:void(0)" onclick="Confirm_1('{{$i->id}}')" class="btn btn-dark" title="Anular">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                            @endcan
                                            @endif
                                        </tr>
            
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($reportType == 2)
                        <div class="col-sm-12 col-md-9">
                            <div class="table-responsive-xxl">
                                <table class="table table-sm table-striped table-bordered mt-1">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white text-center">PRODUCTO</th>
                                            <th class="table-th text-white text-center">MARCA</th>
                                            <th class="table-th text-white text-center">COSTO</th>
                                            <th class="table-th text-white text-center">N° RECIBO</th>
                                            <th class="table-th text-white text-center">CANT</th>
                                            <th class="table-th text-white text-center">TOTAL</th>
                                            <th class="table-th text-white text-center">ORIGEN</th>
                                            <th class="table-th text-white text-center">DESTINO</th>
                                            <th class="table-th text-white text-center">USUARIO</th>
                                            <th class="table-th text-white text-center">FECHA</th>
                                            <th class="table-th text-white text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @if($reportRange == 1 && ($dateFrom == '' || $dateTo == ''))
                                            <tr>
                                                <td colspan="10">
                                                    <h6 class="text-center text-muted">Sin resultados</h6>
                                                </td>
                                            </tr>
                                        @else

                                        @foreach ($transfer as $t)
            
                                        <tr>
                                            <td class="text-center"><h6>{{ $t->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->product->brand }} | {{$t->product->threshing}} | {{$t->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($t->product->cost,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($t->total,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->from_office }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->to_office }}</h6></td>
                                            <td class="text-center"><h6>{{ $t->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($t->created_at)->format('d-m-Y')}}</h6></td>
                                            @if(Carbon\Carbon::parse($t->created_at)->format('d-m-Y') == Carbon\Carbon::today()->format('d-m-Y'))
                                            @can('cancelar_traspaso')
                                            <td>
                                                <a href="javascript:void(0)" onclick="Confirm_2('{{$t->id}}')" class="btn btn-dark" title="Anular">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                            @endcan
                                            @endif
                                        </tr>
            
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($reportType == 3)
                        <div class="col-sm-12 col-md-9">
                            <div class="table-responsive-xxl">
                                <table class="table table-sm table-striped table-bordered mt-1">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white text-center">PRODUCTO</th>
                                            <th class="table-th text-white text-center">MARCA</th>
                                            <th class="table-th text-white text-center">COSTO</th>
                                            {{--<th class="table-th text-white text-center">PRECIO</th>--}}
                                            <th class="table-th text-white text-center">P/VENTA</th>
                                            <th class="table-th text-white text-center">CANT</th>
                                            <th class="table-th text-white text-center">TOTAL</th>
                                            <th class="table-th text-white text-center">UTILIDAD</th>
                                            <th class="table-th text-white text-center">SUCURSAL</th>
                                            <th class="table-th text-white text-center">N° RECIBO</th>
                                            <th class="table-th text-white text-center">USUARIO</th>
                                            <th class="table-th text-white text-center">FECHA</th>
                                            <th class="table-th text-white text-center">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @if($reportRange == 1 && ($dateFrom == '' || $dateTo == ''))
                                            <tr>
                                                <td colspan="10">
                                                    <h6 class="text-center text-muted">Sin resultados</h6>
                                                </td>
                                            </tr>
                                        @else

                                        @foreach ($sale as $s)
            
                                        <tr>
                                            <td class="text-center"><h6>{{ $s->product->code }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->product->brand }} | {{$s->product->threshing}} | {{$s->product->tarp}}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($s->product->cost,2) }}</h6></td>
                                            {{--<td class="text-center"><h6>${{ $s->product->price }}</h6></td>--}}
                                            <td class="text-center"><h6>${{ number_format($s->total / $s->quantity,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->quantity }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($s->total,2) }}</h6></td>
                                            <td class="text-center"><h6>${{ number_format($s->utility,2) }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->office }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->pf }}</h6></td>
                                            <td class="text-center"><h6>{{ $s->user }}</h6></td>
                                            <td class="text-center"><h6>{{Carbon\Carbon::parse($s->created_at)->format('d-m-Y')}}</h6></td>
                                            @if(Carbon\Carbon::parse($s->created_at)->format('d-m-Y') == Carbon\Carbon::today()->format('d-m-Y'))
                                            @can('cancelar_venta')
                                            <td>
                                                <a href="javascript:void(0)" onclick="Confirm_3('{{$s->id}}')" class="btn btn-dark" title="Anular">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                            @endcan
                                            @endif
                                        </tr>
            
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('livewire.report.details')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        flatpickr(document.getElementsByClassName('flatpickr'), {
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

        
        window.livewire.on('show-modal', Msg => {   //evento para mostrar modal
            $('#modalDetails').modal('show')
        })

        window.livewire.on('report-error', msg => {   //evento para los errores del componente
            noty(msg,2)
        })

        window.livewire.on('income-deleted', msg=>{   //evento para eliminar registro
            noty(msg,2)
        });

        window.livewire.on('transfer-deleted', msg=>{   //evento para eliminar registro
            noty(msg,2)
        });

        window.livewire.on('sale-deleted', msg=>{   //evento para eliminar registro
            noty(msg,2)
        });
    })

    function Confirm_1(id){

        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMA ANULAR EL INGRESO?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'
        }).then(function(result){
            if(result.value){
                window.livewire.emit('remove_income', id)
                swal.close()
            }
        })
    }

    function Confirm_2(id){

        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMA ANULAR EL TRASPASO?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'
        }).then(function(result){
            if(result.value){
                window.livewire.emit('remove_transfer', id)
                swal.close()
            }
        })
    }

    function Confirm_3(id){

        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMA ANULAR LA VENTA?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'
        }).then(function(result){
            if(result.value){
                window.livewire.emit('remove_sale', id)
                swal.close()
            }
        })
    }

</script>