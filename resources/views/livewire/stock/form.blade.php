@include('common.modalHead')    <!--inclucion del header del modal-->

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Producto</label>
            <select wire:model="product_id" class="form-control" disabled>
                <option value="Elegir">Elegir</option>
                @foreach ($products as $product)
                    <option value="{{$product->id}}">{{$product->code}}</option>
                @endforeach
            </select>
            @error('product_id')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Sucursal</label>
            <select wire:model="office_id" class="form-control text-uppercase" disabled>
                <option value="Elegir">Elegir</option>
                @foreach ($offices as $office)
                    <option value="{{$office->id}}">{{$office->name}}</option>
                @endforeach
            </select>
            @error('office_id')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Stock Actual</label>
            <input type="text" wire:model.lazy="cant" class="form-control" disabled>
        </div>
        @error('cant')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Tipo de Ingreso</label>
            <select wire:model="type" class="form-control text-uppercase">
                <option value="Elegir" selected>Elegir</option>
                <option value="importacion">importacion</option>
                <option value="compra">compra</option>
                <option value="devolucion">devolucion</option>
                <option value="baja">dar de baja</option>
            </select>
            @error('type')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Cantidad Ingreso/Egreso</label>
            <input type="text" wire:model.lazy="cant2" class="form-control" placeholder="0">
        </div>
        @error('cant2')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>

    @switch($type)

        @case('compra')

            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>Proveedor</label>
                    <select wire:model="prov_id" class="form-control text-uppercase">
                        <option value="Elegir" selected>Elegir</option>
                        @foreach ($providers as $provider)
                            <option value="{{$provider->id}}">{{$provider->description}}</option>
                        @endforeach
                    </select>
                    @error('prov_id')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if($prov_id != 'Elegir')

                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Descripcion</label>
                        <textarea wire:model.lazy="description" class="form-control component-name" placeholder="Descripcion del movimiento..." cols="30" rows="3"></textarea>
                        @error('description')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>N° Recibo</label>
                        <input type="text" wire:model.lazy="pf" class="form-control" placeholder="">
                    </div>
                    @error('pf')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>

            @endif

        @break

        @case('importacion')
            
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>N° Recibo</label>
                    <input type="text" wire:model.lazy="pf" class="form-control" placeholder="">
                </div>
                @error('pf')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Descripcion</label>
                    <textarea wire:model.lazy="description" class="form-control component-name" placeholder="Descripcion del movimiento..." cols="30" rows="3"></textarea>
                    @error('description')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        @break

        @case('devolucion')

            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>N° Recibo</label>
                    <input type="text" wire:model.lazy="pf" class="form-control" placeholder="">
                </div>
                @error('pf')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>

        @break

    @endswitch

</div>

@include('common.modalFooter')