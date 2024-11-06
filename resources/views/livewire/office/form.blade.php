@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Nombre</label>
            <span class="fas fa-edit"></span>
            <input type="text" wire:model.lazy="name" class="form-control component-name" placeholder="Nombre de la sucursal...">
        </div>
        @error('name')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Telefono</label>
            <span class="fas fa-edit"></span>
            <input type="text" wire:model.lazy="phone" class="form-control" placeholder="Telefono de la sucursal...">
        </div>
        @error('phone')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label>Direccion</label>
            <span class="fas fa-edit"></span>
            <input type="text" wire:model.lazy="address" class="form-control" placeholder="Direccion de la sucursal...">
        </div>
        @error('address')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
</div>

@include('common.modalFooter')