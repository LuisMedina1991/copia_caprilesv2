<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title text-uppercase">
                    <b>{{$componentName}} | {{$pageTitle}}</b>
                </h4>
                @can('agregar_registro')
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
                                <th class="table-th text-center text-white">DESCRIPCION</th>
                                <th class="table-th text-center text-white text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($categories as $category)

                            <tr>
                                <td><h6 class="text-center">{{ $category->name }}</h6></td>
                                <td class="text-center">
                                    @can('editar_registro')
                                    <a href="javascript:void(0)" wire:click="Edit({{$category->id}})" class="btn btn-dark mtmobile" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_registro')
                                    <a href="javascript:void(0)" onclick="Confirm('{{$category->id}}')" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                    
                    {{$categories->links()}}

                </div>
            </div>
        </div>
    </div>

    @include('livewire.category.form')

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
        $('#theModal').on('shown.bs.modal', function(e){    //metodo para autofocus a un campo determinado
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