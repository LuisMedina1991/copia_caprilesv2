<ul class="navbar-item flex-row search-ul">
    <!--todas las propiedades publicas usadas en esta vista han sido declaradas y/o inicializadas en el componente "Search.php"
    al igual que el metodo "changePrintingCheckboxStatus()"-->
    <li class="nav-item align-self-center search-animated">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search toggle-search">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <form class="form-inline search-full search" role="search">
            <div class="search-bar">
                <!--directiva de livewire para emitir evento al presionar la tecla indicada-->
                <!--$emit emite el evento que sera escuchado por cualquier controlador que este escuchando el evento-->
                <input id="code" type="text" class="form-control search-form-control ml-lg-auto text-center" placeholder="Código de producto..."
                wire:keydown.enter.prevent="$emit('scan-code', $('#code').val(),$('#sale_price').val(),$('#office').val(),$('#pf').val())">
            </div>
        </form>
    </li>
    <li class="nav-item align-self-center search-animated">
        <form class="form-inline search-full search">
            <div class="search-bar">
                <input id="sale_price" type="text" wire.model="sale_price" class="form-control search-form-control ml-lg-auto text-center" placeholder="Precio de venta...">
            </div>
        </form>
    </li>
    <li class="nav-item align-self-center search-animated">
        <form class="form-inline search-full search">
            <div class="search-bar">
                <input id="pf" type="text" wire.model="pf" class="form-control search-form-control ml-lg-auto text-center" placeholder="N° Proforma...">
            </div>
        </form>
    </li>
    <li class="nav-item align-self-center">
        <form class="form-inline">
            <div class="col-sm-12 col-md-4">
                <select id="office" wire:model="office" class="form-control">
                    @foreach ($offices as $office) <!--iteracion para obtener todas las categorias-->
                        <option value="{{$office->name}}" wire:key="{{$loop->index}}">{{$office->name}}</option>  <!--se obtiene el nombre de las categorias a traves de su id-->
                    @endforeach
                </select>
            </div>
        </form>
    </li>
    <li class="nav-item align-self-center">
        <form class="form-inline">
            <div class="n-check">
                <label class="new-control new-checkbox checkbox-dark">
                    <!--el metodo "changePrintingCheckboxStatus()"" recibe como parametro un estado booleano obtenido con la funcion jquery "is()"
                    que en este caso verifica si el atributo "checked" se encuentra presente en este elemento, accedemos a este elemento
                    tambien usando jquery a traves del id que le asignamos manualmente-->
                    <input type="checkbox" id="printing_checkbox" class="new-control-input" wire:change="changePrintingCheckboxStatus($('#printing_checkbox').is(':checked'))" 
                    value="{{ $allowPrinting }}" {{ $allowPrinting ? 'checked' : '' }}>
                    <span class="new-control-indicator"></span>
                    <h6>Imprimir Ticket</h6>
                </label>
            </div>
        </form>
    </li>
</ul>