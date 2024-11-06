<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title text-uppercase">
                    <b>{{$pageTitle}} | {{$componentName}}</b>
                </h4>
                <ul class="tabs tab-pills">
                    <li>
                        <a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
                    </li>
                </ul>
            </div>

            @include('common.searchbox')

            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white text-center">USUARIO</th>
                                <th class="table-th text-white text-center">TELEFONO</th>
                                <th class="table-th text-white text-center">EMAIL</th>
                                <th class="table-th text-white text-center">ESTADO</th>
                                <th class="table-th text-white text-center">PERFIL</th>
                                {{--<th class="table-th text-white text-center">IMAGEN</th>--}}
                                <th class="table-th text-white text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($users as $user)
                                
                            <tr>
                                <td class="text-center"><h6>{{$user->name}}</h6></td>
                                <td class="text-center"><h6>{{$user->phone}}</h6></td>
                                <td class="text-center"><h6>{{$user->email}}</h6></td>
                                <td class="text-center">
                                    <span class="badge {{$user->status == 'active' ? 'badge-success' : 'badge-danger'}} text-uppercase">{{$user->status}}</span>
                                </td>
                                <td class="text-center text-uppercase"><h6>{{$user->profile}}</h6></td>
                                {{--<td class="text-center">
                                    <span>
                                        <img src="{{ asset('storage/users/' . $user->imagen) }}" alt="imagen" height="70" width="80" class="rounded">
                                    </span>
                                </td>--}}
                                <td class="text-center">
                                    <a href="javascript:void(0)" wire:click="edit({{$user->id}})" class="btn btn-dark mtmobile" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="Confirm('{{$user->id}}')" class="btn btn-dark" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                    {{$users->links()}}
                </div>
            </div>
        </div>
    </div>
    @include('livewire.user.form')
</div>


<script>
    document.addEventListener('DOMContentLoaded', function(){
        
        window.livewire.on('item-added', Msg => {   //evento al agregar registro
            $('#theModal').modal('hide')
            noty(Msg)
        })

        window.livewire.on('item-updated', Msg => { //evento al actualizar registro
            $('#theModal').modal('hide')
            noty(Msg)
        })

        window.livewire.on('item-deleted', Msg => { //evento al eliminar registro
            noty(Msg)
        })

        window.livewire.on('hide-modal', Msg => {   //evento para cerrar modal
            $('#theModal').modal('hide')
        })

        window.livewire.on('show-modal', Msg => {   //evento para mostral modal
            $('#theModal').modal('show')
        })

        window.livewire.on('user-with-sales', Msg => {  //evento para notificar que usuario tiene ventas asociadas
            noty(Msg)
        })

        $('#theModal').on('shown.bs.modal', function(e){    //metodo para autofocus al campo nombre
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