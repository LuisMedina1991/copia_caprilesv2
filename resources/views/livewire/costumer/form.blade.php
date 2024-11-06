@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" wire:model.lazy="description" class="form-control component-name" placeholder="Nombre del cliente...">
            @error('description')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Telefono</label>
            <input type="text" wire:model.lazy="phone" class="form-control" placeholder="Telefono del cliente...">
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Fax</label>
            <input type="text" wire:model.lazy="fax" class="form-control" placeholder="Fax del cliente...">
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>NIT</label>
            <input type="text" wire:model.lazy="nit" class="form-control" placeholder="NIT del cliente...">
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Email</label>
            <input type="text" wire:model.lazy="email" class="form-control" placeholder="Email del cliente...">
        </div>
    </div>
</div>

@include('common.modalFooter')