<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title text-uppercase">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                <div class="form-inline">
                    <ul>
                        <h5 wire:ignore class="mr-5">VALOR TOTAL DE INVENTARIO: ${{ number_format($my_total,2)}}</h5>
                        <br>
                        <a href="{{ url('report_stock/pdf' . '/' . $my_total) }}" class="btn btn-dark btn-block" target="_blank">
                            Generar PDF
                        </a>
                    </ul>
                </div>
            </div>
            @include('common.searchbox')
            <div class="widget-content">
                <div class="table-responsive-xxl">
                    <table class="table table-striped table-bordered mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white text-center">CODIGO DE PRODUCTO</th>
                                <th class="table-th text-white text-center">DESCRIPCION</th>
                                <th class="table-th text-white text-center">COSTO</th>
                                <th class="table-th text-white text-center">PRECIO</th>
                                <th class="table-th text-white text-center">ESTADO</th>
                                @foreach($offices as $office)
                                <th class="table-th text-center text-white">{{$office->name}}</th>
                                @endforeach
                                <th class="table-th text-white text-center">TOTALES</th>
                                <th class="table-th text-center text-white">ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                            <tr>
                                <td><h6 class="text-center">{{$stock->code}}</h6></td>
                                <td><h6 class="text-center">{{$stock->brand}} | {{$stock->threshing}} | {{$stock->tarp}}</h6></td>
                                <td><h6 class="text-center">{{number_format($stock->cost,2)}}</h6></td>
                                <td><h6 class="text-center">{{number_format($stock->price,2)}}</h6></td>
                                <td class="text-center">
                                    <span class="badge {{$stock->state->name == 'ok' ? 'badge-success' : 'badge-danger'}} text-uppercase">{{$stock->state->name}}</span>
                                </td>

                                @foreach($stock->offices as $var)
                                    <td class="text-center"><h6>{{$var->pivot->stock}}</h6>
                                        @can('editar_stock')
                                        <a href="javascript:void(0)" wire:click="Edit({{$var->pivot->id}})" class="btn btn-dark mtmobile" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" wire:click="Charge({{$var->pivot->id}})" class="btn btn-dark mtmobile" title="Transferir" data-toggle="modal" data-target="#theModal2">
                                            <i class="fas fa-truck"></i>
                                        </a>
                                        @endcan
                                    </td>
                                @endforeach
                                <td><h6 class="text-center bg-success">{{$stock->offices->sum('pivot.stock')}}</h6></td>
                                @can('eliminar_stock')
                                <td class="text-center">
                                    <a href="javascript:void(0)" onclick="Confirm({{$stock->id}})" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    {{$stocks->links()}}

                </div>
            </div>
        </div>
    </div>

    @include('livewire.stock.form')
    @include('livewire.stock.form2')

</div>


<script> 
    document.addEventListener('DOMContentLoaded', function(){

        window.livewire.on('show-modal', msg=>{     //evento para mostral modal
            $('#theModal').modal('show')
        });

        window.livewire.on('item-added', msg=>{     //evento al agregar registro
            $('#theModal').modal('hide')
            noty(msg)
        });

        window.livewire.on('item-deleted', msg=>{   //evento al eliminar registro
            noty(msg)
        });

        window.livewire.on('item-updated', msg=>{   //evento al actualizar registro
            $('#theModal').modal('hide')
            noty(msg)
        });

        $('#theModal').on('shown.bs.modal', function(e){    //metodo para autofocus a campo determinado
            $('.component-name').focus()
        });

        window.livewire.on('income-error', Msg => {
            noty(Msg, 2)
        });

        window.livewire.on('show-modal2', msg=>{
            $('#theModal2').modal('show')
        });

        window.livewire.on('item-transfered', msg=>{
            $('#theModal2').modal('hide')
            noty(msg)
        });

        window.livewire.on('cover-error', msg=>{    //evento al eliminar registro
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

                window.livewire.emit('destroy',id)
                swal.close()
            }
        })
    }

</script>