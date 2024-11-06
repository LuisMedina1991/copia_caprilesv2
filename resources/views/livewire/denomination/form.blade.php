@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Tipo</label>
            <select wire:model="type" class="form-control">
                <option value="Elegir">Elegir</option> 
                <option value="billete">billete</option>
                <option value="moneda">moneda</option>
                <option value="otro">otro</option>
            </select>
            @error('type')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <label>Valor</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <span class="fas fa-edit"></span>
                </span>
            </div>
            <input type="text" wire:model.lazy="value" class="form-control" placeholder="0.00" maxlength="25">
        </div>
        @error('value')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
</div>

@include('common.modalFooter')