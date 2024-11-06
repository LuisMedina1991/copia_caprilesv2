@include('common.modalHead')

<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label>Titular</label>
            <select wire:model="company_id" class="form-control">
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
            <select wire:model="bank_id" class="form-control">
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
            <select wire:model="type" class="form-control">
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
            <select wire:model="currency" class="form-control">
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
            <label>Saldo</label>
            <input type="text" wire:model.lazy="amount" class="form-control" placeholder="0.00">
            @error('amount')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

@include('common.modalFooter')