<div class="connect-sorting">   
    <div class="connect-sorting-content">
        <div class="card simple-title-task ui-sortable-handle">
            <div class="card-body">
                @if($itemsQuantity > 0) <!--validar si carrito contiene productos-->
                <!--clase tblscroll para utilizar plugin para scrollbar personalizado-->
                <div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
                    <table class="table table-bordered table-striped mt-1"> <!--tabla-->
                        <thead class="text-white" style="background: #3B3F5C">  <!--encabezado de tabla-->
                            <tr>
                                {{--<th class="table-th text-center text-white" width="10%">IMAGEN</th>--}}
                                <th class="table-th text-center text-white">DESCRIPCION</th>
                                <th class="table-th text-center text-white">SUCURSAL</th>
                                <th class="table-th text-center text-white">PRECIO</th>
                                <th width="13%" class="table-th text-center text-white">CANTIDAD</th>
                                <th class="table-th text-center text-white">IMPORTE</th>
                                <th class="table-th text-center text-white">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody> <!--cuerpo de tabla-->                        
                            @foreach ($cart as $item)   <!--iteracion para obtener todos los productos del carrito-->
                            <tr>
                                {{--<td class="text-center table-th">
                                    @if($item->attributes[0] != null)
                                        <span>
                                            <img src="{{ asset('storage/products/' . $item->attributes[0]) }}" alt="imagen de producto" height="90" width="90" class="rounded">
                                        </span>
                                    @else
                                        <span>
                                            <img src="{{ asset('storage/noimg.jpg') }}" alt="imagen de producto" height="90" width="90" class="rounded">
                                        </span>
                                    @endif
                                </td>--}}
                                <td><h6 class="text-center">{{ $item->attributes[0] . ' ' .$item->attributes[1]. ' ' .$item->attributes[2]. ' ' .$item->attributes[3]}}</h6></td>
                                <td><h6 class="text-center">{{ $item->name }}</h6></td>
                                <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                                <td class="text-center">${{ number_format($item->price,2) }}</td>
                                <td>
                                    <!--asignamos un id r+id para recuperar lo que contenga el input y hacer operaciones en el backend-->
                                    <!--directiva de livewire para que al cambiar el contenido el input hacer llamado a metodo del controlador-->
                                    <!--metodo updateQty pasamos como parametros (id_producto,id_input)-->
                                    <input type="text" id="r{{$item->id}}" wire:change="updateQty({{$item->id}}, $('#r' + {{$item->id}}).val() )"
                                    style="font-size: 1rem!important" class="form-control text-center" value="{{$item->quantity}}">
                                </td>
                                <td class="text-center">
                                    <h6>
                                        <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                                        <!--aqui obtenemos el importe multiplicando el precio del producto * cantidad de productos-->
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </h6>
                                </td>
                                <td class="text-center">
                                    <button wire:click.prevent="increaseQty({{ $item->id }})" class="btn btn-dark mbmobile" title="Aumentar 1">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button wire:click.prevent="decreaseQty({{ $item->id }})" class="btn btn-dark mbmobile" title="Disminuir 1">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button onclick="Confirm('{{ $item->id }}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark mbmobile" title="Quitar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @else   <!--si carrito esta vacio se muestra el siguiente texto-->
                <h5 class="text-center text-muted">Agrega productos a la venta</h5>
                @endif

                <!--div para mostrar texto al cuando el sistema este en proceso de registrar venta-->
                <!--directiva de livewire para mostrar h4 cuando se este llevando a cabo el evento especificado-->
                <div wire:loading.inline wire:target="saveSale">
                    <h4 class="text-danger text-center">Guardando venta...</h4>
                </div>
                
            </div>
        </div>
    </div>
</div>