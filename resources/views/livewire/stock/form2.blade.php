<div wire:ignore.self class="modal fade" id="theModal2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h5 class="modal-title text-white text-uppercase">
            <b>{{$componentName}}</b> | {{ $formTitle}}
          </h5>
          <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
        </div>
        <div class="modal-body">
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
                        <label>Sucursal Origen</label>
                        <select wire:model="office_id" class="form-control" disabled>
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
                        <label>Sucursal Destino</label>
                        <select wire:model="office_id_2" class="form-control">
                            <option value="Elegir">Elegir</option>
                            @foreach ($offices2 as $office)
                                <option value="{{$office->id}}">{{$office->name}}</option>
                            @endforeach
                        </select>
                        @error('office_id_2')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="text" wire:model.lazy="cant" class="form-control" placeholder="0" disabled>
                    </div>
                    @error('cant')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Cant. Traspaso</label>
                        <input type="number" wire:model.lazy="cant2" class="form-control" placeholder="0">
                        @error('cant2')
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
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
            <button type="button" wire:click.prevent="Transfer()" class="btn btn-dark close-modal">GUARDAR</button>
        </div>
    </div>
</div>
</div>