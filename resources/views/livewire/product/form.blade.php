@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Medida</label>
            <input type="text" wire:model.lazy="descripcion" class="form-control component-name" placeholder="Medida del producto...">
            @error('descripcion')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Codigo</label>
            <input type="text" wire:model.lazy="code" class="form-control" placeholder="Codigo del producto...">
            @error('code')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Marca</label>
            <input type="text" wire:model.lazy="marca" class="form-control" placeholder="S/M si no se conoce...">
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Aro</label>
            <input type="text" wire:model.lazy="aro" class="form-control" placeholder="S/A si no corresponde...">
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Trilla</label>
            <input type="text" wire:model.lazy="trilla" class="form-control" placeholder="S/T si no corresponde...">
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Lona</label>
            <input type="text" wire:model.lazy="lona" class="form-control" placeholder="S/L si no corresponde...">
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Costo</label>
            <input type="text" wire:model.lazy="cost" class="form-control" placeholder="0.00">
            @error('cost')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Precio</label>
            <input type="text" wire:model.lazy="price" class="form-control" placeholder="0.00">
            @error('price')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Estado</label>
            <select wire:model="state" class="form-control">
                <option value="Elegir" selected>Elegir</option>
                @foreach($states as $state)                                        
                <option value="{{$state->id}}">{{$state->name}}</option>
                @endforeach
            </select>
            @error('state')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Categoria</label>
            <select wire:model="catId" class="form-control">
                @foreach($categories as $cat)                                        
                <option value="{{$cat->id}}">{{$cat->name}}</option>
                @endforeach
            </select>
            @error('catId')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="form-group">
            <label>Subcategoria</label>
            <select wire:model="subId" class="form-control">
                <option value="Elegir">Elegir</option>
                @foreach($subcategories as $sub)                                      
                <option value="{{$sub->id}}">{{$sub->name}}</option>
                @endforeach
            </select>
            @error('subId')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    {{--<div class="col-sm-12">
        <div class="form-group custom-file">
            <!--directiva de livewire para relacionar el input con una propiedad publica y especificando el tipo de archivos aceptados-->
            <input type="file" class="custom-file-input form-control" wire:model="image" accept="image/x-png, image/gif, image/jpeg">
            <label class="custom-file-label">IMAGEN {{ $image }}</label>
            @error('image') <!--directiva de blade para capturar el error al validar la propiedad publica-->
                <span class="text-danger er">{{ $message }}</span>  <!--mensaje recibido desde el controlador-->
            @enderror
        </div>
    </div>--}}
</div>

@include('common.modalFooter')