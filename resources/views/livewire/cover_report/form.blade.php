<div wire:ignore.self class="modal fade" id="theModal2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h5 class="modal-title text-white">
            DIFERENCIA POR T/C
          </h5>
          <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Monto</label>
                        <input type="text" wire:model.lazy="amount" class="form-control" placeholder="0.00">
                        @error('amount')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
            <button type="button" wire:click.prevent="Force_Balance()" class="btn btn-dark close-modal">GUARDAR</button>
        </div>
    </div>
</div>
</div>