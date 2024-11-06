<div wire:ignore.self class="modal fade" id="data_import_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white text-uppercase">
                <b>IMPORTAR DATOS | {{$componentName}}</b>
                </h5>
                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Seleccione archivo</label>
                            <input type="file" wire:model.lazy="data_to_import" class="form-control">
                            @error('data_to_import')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
                <button type="button" wire:click.prevent="ImportData()" class="btn btn-dark close-modal">IMPORTAR</button>
            </div>
        </div>
    </div>
</div>