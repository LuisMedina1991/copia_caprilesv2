@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Referencia</label>
            <input type="text" wire:model.lazy="reference" class="form-control component-name" placeholder="Referencia de la factura...">
            @error('reference')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Tipo de Factura</label>
            <select wire:model="type" class="form-control">
                <option value="Elegir">Elegir</option>
                <option value="normal">Normal</option>
                <option value="acumulativa">Acumulativa</option>
            </select>
            @error('type')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Descripcion</label>
            <textarea wire:model.lazy="description" class="form-control" placeholder="Detalles de la factura..." cols="30" rows="3"></textarea>
            @error('description')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Monto</label>
            <input type="text" wire:model.lazy="amount" class="form-control" placeholder="0.00">
            @error('amount')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

@include('common.modalFooter')