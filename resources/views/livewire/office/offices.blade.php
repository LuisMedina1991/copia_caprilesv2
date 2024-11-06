<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title text-uppercase">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                @can('crear_sucursal')
                <ul class="tabs tab-pills">
                    <li>
                        <a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
                    </li>
                </ul>
                @endcan
            </div>

            @include('common.searchbox')

            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white text-center">DESCRIPCION</th>
                                <th class="table-th text-center text-white">DIRECCION</th>
                                <th class="table-th text-center text-white">TELEFONO</th>
                                <th class="table-th text-center text-white">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($offices as $office)

                            <tr>
                                <td><h6 class="text-center">{{ $office->name }}</h6></td>
                                <td><h6 class="text-center">{{ $office->address }}</h6></td>
                                <td><h6 class="text-center">{{ $office->phone }}</h6></td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" wire:click="Edit({{$office->id}})" class="btn btn-dark mtmobile" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @can('record_delete')
                                    <a href="javascript:void(0)" onclick="Confirm('{{$office->id}}')" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                    
                    {{$offices->links()}}

                </div>
            </div>
        </div>
    </div>

    @include('livewire.office.form')

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

</script>