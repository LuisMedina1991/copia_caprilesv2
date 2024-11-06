<div class="row mt-3">
    <div class="col-sm-12">
        <div class="connect-sorting">
            <h5 class="text-center mb-2">DENOMINACIONES</h5>
            <div class="container">
                <div class="row">
                    @foreach ($denominations as $d) <!--iteracion de los datos almacenados en variable pasada desde controlador-->
                        <div class="col-sm mt-2">
                            <!--directiva click de livewire que hace llamado al metodo del componente pasandole el valor-->
                            <button wire:click.prevent="ACash({{ $d->value }} )" class="btn btn-dark btn-block den">
                                <!--validar si valor del boton es mayor a 0 muestra el valor de la columna value en el texto del boton-->
                                <!--caso contrario el texto del boton muestra 'Exacto'-->
                                <!--funcion number_format de php en este caso resive (numero,cantidad de decimales,valor por defecto,reemplazar valor por defecto por)-->
                                {{ $d->value > 0 ? '$' . number_format($d->value,2, '.', '') : 'Exacto' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="connect-sorting-content mt-4">
                <div class="card simple-title-task ui-sortable-handle">
                    <div class="card-body">
                        <div class="input-group input-group-md mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-gp hideonsm" style="background: #3B3F5C; color:white">Efectivo</span>
                            </div>
                            <!--directiva de livewire para relacionar el input con una propiedad publica-->
                            <input type="number" id="cash" wire:model="efectivo" class="form-control text-center" value="{{ $efectivo }}">
                            <div class="input-group-append">
                                <!--directiva click de livewire para dejar el valor de la propiedad publica en 0-->
                                <!--metodo $set de livewire recibe por parametros (propiedad publica,valor a establecer)-->
                                <span wire:click="$set('efectivo', 0)" class="input-group-text" style="background: #3B3F5C; color:white">
                                    <i class="fas fa-backspace fa-2x"></i>
                                </span>
                            </div>
                        </div>
                        <!--funcion number_format de php para dar formato decimal recibe 2 parametros (numero,cantidad de decimales)-->
                        <h4 class="text-muted">Cambio: ${{ number_format($change,2) }}</h4>
                        <div class="row justify-content-between mt-5">
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                @if($total > 0) <!--validacion para mostrar boton-->
                                <!--en este caso la funcion Confirm recibe (id,accion/metodo/evento,mensaje)-->
                                <button onclick="Confirm('', 'clearCart', '¿SEGURO DE ELIMINAR EL CARRITO?')" class="btn btn-dark mtmobile">
                                    CANCELAR
                                </button>
                                @endif
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                @if($efectivo >= $total && $total > 0)  <!--validacion para mostrar boton-->
                                <!--directiva de livewire para llamar a metodo del controlador-->
                                <button class="btn btn-dark btn-md btn-block" wire:click.prevent="saveSale($('#printing_checkbox').is(':checked'),$('#pf').val())">
                                    GUARDAR
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>