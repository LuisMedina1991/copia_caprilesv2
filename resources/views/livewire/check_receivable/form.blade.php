@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Cliente</label>
            <select wire:model="costumer" class="form-control">
                <option value="Elegir">Elegir</option>
                @foreach($costumers as $costumer)                                      
                <option value="{{$costumer->id}}">{{$costumer->description}}</option>
                @endforeach
            </select>
            @error('costumer')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Banco</label>
            <select wire:model="bank" class="form-control">
                <option value="Elegir">Elegir</option>
                @foreach($banks as $bank)                                      
                <option value="{{$bank->id}}">{{$bank->description}}</option>
                @endforeach
            </select>
            @error('bank')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>N° Cheque</label>
            <input type="text" wire:model.lazy="number" class="form-control" placeholder="123">
            @error('number')
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
    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Descripcion</label>
            <textarea wire:model.lazy="description" class="form-control" placeholder="Detalles de la transaccion..." cols="30" rows="3"></textarea>
            @error('description')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

@include('common.modalFooter')