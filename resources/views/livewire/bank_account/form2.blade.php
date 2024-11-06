<div wire:ignore.self class="modal fade" id="theModal2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h5 class="modal-title text-white text-uppercase">
            <b>{{$componentName}}</b> | Actualizar
          </h5>
          <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Propietario</label>
                        <select wire:model="company_id" class="form-control" disabled>
                            <option value="Elegir">Elegir</option>
                            @foreach ($companies as $company)
                                <option value="{{$company->id}}">{{$company->description}}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Banco</label>
                        <select wire:model="bank_id" class="form-control" disabled>
                            <option value="Elegir">Elegir</option>
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->description}}</option>
                            @endforeach
                        </select>
                        @error('bank_id')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Tipo de cuenta</label>
                        <select wire:model="type" class="form-control" disabled>
                            <option value="Elegir" selected>Elegir</option>
                            <option value="caja de ahorros">caja de ahorros</option>
                            <option value="cuenta corriente">cuenta corriente</option>
                        </select>
                        @error('type')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Moneda</label>
                        <select wire:model="currency" class="form-control" disabled>
                            <option value="Elegir" selected>Elegir</option>
                            <option value="bolivianos">bolivianos</option>
                            <option value="dolares">dolares</option>
                        </select>
                        @error('currency')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Saldo Actual</label>
                        <input type="text" wire:model="amount" class="form-control" placeholder="Saldo de la cuenta..." disabled>
                        @error('amount')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Accion</label>
                        <select wire:model="action" class="form-control">
                            <option value="Elegir">Elegir</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                        @error('action')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if($action == 'ingreso')

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Detalles de ingreso</label>
                            <textarea wire:model.lazy="income_description" class="form-control" placeholder="Detalles del movimiento..." cols="30" rows="3"></textarea>
                            @error('income_description')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Monto de ingreso</label>
                            <input type="text" wire:model.lazy="income_amount" class="form-control" placeholder="0.00">
                            @error('income_amount')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @endif

                @if($action == 'egreso')

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Detalles de egreso</label>
                            <textarea wire:model.lazy="discharge_description" class="form-control" placeholder="Detalles del movimiento..." cols="30" rows="3"></textarea>
                            @error('discharge_description')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Monto de egreso</label>
                            <input type="text" wire:model.lazy="discharge_amount" class="form-control" placeholder="0.00">
                            @error('discharge_amount')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @endif

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
            <button type="button" wire:click.prevent="Update()" class="btn btn-dark close-modal">ACTUALIZAR</button>
        </div>
    </div>
</div>
</div>