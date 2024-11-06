@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" wire:model.lazy="description" class="form-control component-name" placeholder="Nombre del banco...">
        </div>
        @error('description')
            <span class="text-danger er">{{ $message }}</span>
        @enderror
    </div>
</div>

@include('common.modalFooter')