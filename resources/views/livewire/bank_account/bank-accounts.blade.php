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
                                <th class="table-th text-white text-center">PROPIETARIO</th>
                                <th class="table-th text-white text-center">BANCO</th>
                                <th class="table-th text-white text-center">TIPO DE CUENTA</th>
                                <th class="table-th text-white text-center">MONEDA DE CUENTA</th>
                                <th class="table-th text-white text-center">SALDO</th>
                                <th class="table-th text-center text-white">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                            <tr>
                                <td><h6 class="text-center">{{$account->company}}</h6></td>
                                <td><h6 class="text-center">{{$account->bank}}</h6></td>
                                <td><h6 class="text-center">{{$account->type}}</h6></td>
                                <td><h6 class="text-center">{{$account->currency}}</h6></td>
                                <td><h6 class="text-center">${{number_format($account->amount,2)}}</h6></td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" wire:click="Edit({{$account->id}})" class="btn btn-dark mtmobile" title="Editar" data-toggle="modal" data-target="#theModal2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Confirm({{$account->id}})" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    {{$accounts->links()}}

                </div>
            </div>
        </div>
    </div>
    @include('livewire.bank_account.form')
    @include('livewire.bank_account.form2')
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
            $('#theModal2').modal('hide')
            noty(msg)
        });

        $('#theModal').on('shown.bs.modal', function(e){    //metodo para autofocus al campo nombre
            $('.component-name').focus()
        });

        window.livewire.on('show-modal2', msg=>{     //evento para mostral modal
            $('#theModal2').modal('show')
        });

        window.livewire.on('error-message', msg => {   //evento para mostrar los mensajes de error del componente
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