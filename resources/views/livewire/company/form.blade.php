@include('common.modalHead')    <!--inclucion del header del modal-->

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" wire:model.lazy="description" class="form-control component-name" placeholder="Nombre de la empresa...">
            @error('description')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>NIT</label>
            <input type="text" wire:model.lazy="nit" class="form-control" placeholder="NIT de la empresa...">
            @error('nit')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Tipo</label>
            <input type="text" wire:model.lazy="type" class="form-control" placeholder="Tipo de empresa...">
            @error('type')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Rubro</label>
            <input type="text" wire:model.lazy="category" class="form-control" placeholder="Rubro de la empresa...">
            @error('category')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Direccion</label>
            <input type="text" wire:model.lazy="address" class="form-control" placeholder="Direccion de la empresa...">
            @error('address')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

@include('common.modalFooter')