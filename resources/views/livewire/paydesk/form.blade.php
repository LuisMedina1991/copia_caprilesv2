@include('common.modalHead')

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Descripcion</label>
            <textarea wire:model.lazy="description" class="form-control component-name" placeholder="Descripcion del movimiento..." cols="30" rows="3"></textarea>
            @error('description')
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

    @switch($action)

        @case('egreso')

            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>Tipo de Egreso</label>
                    <select wire:model="type" class="form-control text-uppercase">
                        <option value="Elegir">Elegir</option>
                        <option value="cheques por cobrar">Cheque</option>
                        <option value="caja general">Variado</option>
                        <option value="gimnasio">Gimnasio</option>
                        <option value="facturas/impuestos">Facturas</option>
                        <option value="comisiones">comisiones</option>
                        <option value="perdida por devolucion">devolucion</option>
                        <option value="anticreticos">Anticreticos</option>
                        <option value="proveedores por pagar">Proveedores</option>
                        <option value="consignaciones">Consignaciones</option>
                        <option value="deposito/retiro">Deposito/Retiro</option>
                        <option value="otros por pagar">Otros por Pagar</option>
                        <option value="otros por cobrar">Otros por Cobrar</option>
                        <option value="diferencia por t/c">Diferencia por T/C</option>
                        <option value="otros proveedores">otros proveedores</option>
                        <option value="clientes por cobrar">Clientes por Cobrar</option>
                        <option value="gastos de importacion">Mercaderia en transito</option>
                        <option value="gastos gorky">Gastos Gorky</option>
                        <option value="gastos importadora">Gastos de Importadora</option>
                        <option value="gastos construccion">Gastos de Construccion</option>
                    </select>
                    @error('type')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @switch($type)

                @case('perdida por devolucion')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Perdida</label>
                            <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="0.00">
                            @error('temp3')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @break

                @case('otros proveedores')

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($other_providers as $other)
                                    <option value="{{$other->id}}">{{$other->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                @break

                @case('consignaciones')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($appropiations as $app)
                                    <option value="{{$app->id}}">{{$app->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <select wire:model="temp2" class="form-control">
                                    <option value="Elegir">Elegir</option>
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->description}}</option>
                                    @endforeach
                                </select>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('facturas/impuestos')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($bills as $bill)
                                    <option value="{{$bill->id}}">{{$bill->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('clientes por cobrar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Clientes por Cobrar</label>
                            <select wire:model="temp" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->description}}</option>
                                @endforeach
                            </select>
                            @error('temp')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @break

                @case('otros por cobrar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="Referencia de la deuda...">
                            @error('temp3')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @break

                @case('otros por pagar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($payables as $pay)
                                    <option value="{{$pay->id}}">{{$pay->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('anticreticos')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($antics as $antic)
                                    <option value="{{$antic->id}}">{{$antic->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <select wire:model="temp2" class="form-control">
                                    <option value="Elegir">Elegir</option>
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->description}}</option>
                                    @endforeach
                                </select>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('deposito/retiro')
                
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Cuenta Bancaria</label>
                            <select wire:model="bankAccountId" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($allBankAccounts as $bank_account)
                                    <option value="{{$bank_account->id}}">{{$bank_account->bank->description}} || {{$bank_account->type}} || {{$bank_account->currency}} || {{$bank_account->company->description}}</option>
                                @endforeach
                            </select>
                            @error('bankAccountId')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($bankAccountId != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model="bankAccountBalance" class="form-control" disabled>
                                @error('bankAccountBalance')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('proveedores por pagar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Proveedores</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($providers as $provi)
                                    <option value="{{$provi->id}}">{{$provi->description}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <select wire:model="temp2" class="form-control">
                                    <option value="Elegir">Elegir</option>
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->description}}</option>
                                    @endforeach
                                </select>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('cheques por cobrar')
                    
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Clientes</label>
                            <select wire:model="chc1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->description}}</option>
                                @endforeach
                            </select>
                            @error('chc1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Bancos</label>
                            <select wire:model="chc2" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($banks as $bank)
                                    <option value="{{$bank->id}}">{{$bank->description}}</option>
                                @endforeach
                            </select>
                            @error('chc2')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>N° Cheque</label>
                            <input type="text" wire:model.lazy="chc3" class="form-control" placeholder="123">
                            @error('chc3')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                @break

            @endswitch

            @if($type != 'Elegir')

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Monto de Egreso</label>
                        <input type="text" wire:model.lazy="discharge_amount" class="form-control" placeholder="0.00">
                        @error('discharge_amount')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            @endif
            
        @break

        @case('ingreso')
            
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>Tipo de Ingreso</label>
                    <select wire:model="type" class="form-control text-uppercase">
                        <option value="Elegir">Elegir</option>
                        <option value="cheques por cobrar">Cheque</option>
                        <option value="caja general">Variado</option>
                        <option value="utilidad">Utilidad</option>
                        <option value="gimnasio">Gimnasio</option>
                        <option value="facturas/impuestos">Facturas</option>
                        <option value="anticreticos">Anticreticos</option>
                        <option value="deposito/retiro">Deposito/Retiro</option>
                        <option value="otros por pagar">Otros por Pagar</option>
                        <option value="cambio de llantas">Cambio de Llantas</option>
                        <option value="diferencia por t/c">Diferencia por T/C</option>
                        <option value="otros por cobrar">Otros por Cobrar</option>
                        <option value="otros proveedores">otros proveedores</option>
                        <option value="clientes por cobrar">Clientes por Cobrar</option>
                        <option value="gastos de importacion">Mercaderia en transito</option>
                    </select>
                    @error('type')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @switch($type)

                @case('otros proveedores')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Tipo de Deuda</label>
                            <select wire:model="temp" class="form-control">
                                <option value="Elegir">Elegir</option>
                                <option value="Nueva">Nueva</option>
                                <option value="Existente">Existente</option>
                            </select>
                            @error('temp')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @switch($temp)

                        @case('Nueva')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="Referencia de la deuda...">
                                    @error('temp3')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Detalles</label>
                                    <textarea wire:model="temp4" class="form-control" placeholder="Detalles del nuevo ingreso..." cols="30" rows="3"></textarea>
                                    @error('temp4')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        @break

                        @case('Existente')

                            @if($temp1 != 'Elegir')

                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Saldo</label>
                                        <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Descripcion de la deuda</label>
                                        <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                        @error('temp2')
                                            <span class="text-danger er">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            @endif

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <select wire:model="temp1" class="form-control">
                                        <option value="Elegir">Elegir</option>
                                        @foreach ($other_providers as $other)
                                            <option value="{{$other->id}}">{{$other->reference}}</option>
                                        @endforeach
                                    </select>
                                    @error('temp1')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if($temp1 != 'Elegir')

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Detalles</label>
                                        <textarea wire:model="temp4" class="form-control" placeholder="Detalles del nuevo ingreso..." cols="30" rows="3"></textarea>
                                        @error('temp4')
                                            <span class="text-danger er">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            @endif

                        @break

                    @endswitch

                @break

                @case('gastos de importacion')
                        
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Detalles del gasto</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($imports as $import)
                                    <option value="{{$import->id}}">{{$import->description}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                @break

                @case('facturas/impuestos')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Tipo de Deuda</label>
                            <select wire:model="temp" class="form-control">
                                <option value="Elegir">Elegir</option>
                                <option value="Nueva">Nueva</option>
                                <option value="Existente">Existente</option>
                            </select>
                            @error('temp')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @switch($temp)

                        @case('Nueva')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="Referencia de la deuda...">
                                    @error('temp3')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea wire:model="temp4" class="form-control" placeholder="Descripcion para la deuda..." cols="30" rows="3"></textarea>
                                    @error('temp4')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        @break

                        @case('Existente')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <select wire:model="temp1" class="form-control">
                                        <option value="Elegir">Elegir</option>
                                        @foreach ($bills as $bill)
                                            <option value="{{$bill->id}}">{{$bill->reference}}</option>
                                        @endforeach
                                    </select>
                                    @error('temp1')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if($temp1 != 'Elegir')

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Descripcion de la deuda</label>
                                        <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                        @error('temp2')
                                            <span class="text-danger er">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Saldo</label>
                                        <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                                    </div>
                                </div>

                            @endif

                        @break

                    @endswitch

                @break

                @case('otros por pagar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Tipo de Deuda</label>
                            <select wire:model="temp" class="form-control">
                                <option value="Elegir">Elegir</option>
                                <option value="Nueva">Nueva</option>
                                <option value="Existente">Existente</option>
                            </select>
                            @error('temp')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @switch($temp)

                        @case('Nueva')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="Referencia de la deuda...">
                                    @error('temp3')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        @break

                        @case('Existente')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <select wire:model="temp1" class="form-control">
                                        <option value="Elegir">Elegir</option>
                                        @foreach ($payables as $pay)
                                            <option value="{{$pay->id}}">{{$pay->reference}}</option>
                                        @endforeach
                                    </select>
                                    @error('temp1')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
        
                            @if($temp1 != 'Elegir')

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Descripcion de la deuda</label>
                                        <input type="text" wire:model.lazy="temp2" class="form-control" disabled>
                                        @error('temp2')
                                            <span class="text-danger er">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Saldo</label>
                                        <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                                    </div>
                                </div>

                            @endif

                        @break

                    @endswitch

                @break

                @case('anticreticos')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Tipo de Anticretico</label>
                            <select wire:model="temp" class="form-control">
                                <option value="Elegir">Elegir</option>
                                <option value="Nuevo">Nuevo</option>
                                <option value="Existente">Existente</option>
                            </select>
                            @error('temp')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @switch($temp)

                        @case('Nuevo')
                        
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <input type="text" wire:model.lazy="temp3" class="form-control" placeholder="Referencia para el anticretico...">
                                </div>
                            </div>

                        @break

                        @case('Existente')

                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Referencia</label>
                                    <select wire:model="temp1" class="form-control">
                                        <option value="Elegir">Elegir</option>
                                        @foreach ($antics as $antic)
                                            <option value="{{$antic->id}}">{{$antic->reference}}</option>
                                        @endforeach
                                    </select>
                                    @error('temp1')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if($temp1 != 'Elegir')

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Descripcion de la deuda</label>
                                        <select wire:model="temp2" class="form-control">
                                            <option value="Elegir">Elegir</option>
                                            @foreach ($details as $detail)
                                                <option value="{{$detail->id}}">{{$detail->description}}</option>
                                            @endforeach
                                        </select>
                                        @error('temp2')
                                            <span class="text-danger er">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            @endif

                            @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Saldo</label>
                                        <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                                    </div>
                                </div>

                            @endif

                        @break

                    @endswitch

                @break

                @case('otros por cobrar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Referencia</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($others as $other)
                                    <option value="{{$other->id}}">{{$other->reference}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <select wire:model="temp2" class="form-control">
                                    <option value="Elegir">Elegir</option>
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->description}}</option>
                                    @endforeach
                                </select>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('cheques por cobrar')
                    
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Clientes</label>
                            <select wire:model="chc1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($c_clients as $client)
                                    <option value="{{$client->id}}">{{$client->description}}</option>
                                @endforeach
                            </select>
                            @error('chc1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($chc1 != 'Elegir' && $chc2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>N° Cheque</label>
                                <input type="text" wire:model.lazy="temp3" class="form-control" disabled>
                                @error('temp3')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Banco</label>
                                <input type="text" wire:model.lazy="temp" class="form-control" disabled>
                                @error('temp')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                                @error('balance')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                    @if($chc1 != 'Elegir')

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Detalles del Cheque</label>
                            <select wire:model="chc2" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($details as $detail)
                                    <option value="{{$detail->id}}">{{$detail->description}}</option>
                                @endforeach
                            </select>
                            @error('chc2')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @endif

                @break

                @case('clientes por cobrar')

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Clientes</label>
                            <select wire:model="temp1" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->description}}</option>
                                @endforeach
                            </select>
                            @error('temp1')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($temp1 != 'Elegir' && $temp2 != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model.lazy="balance" class="form-control" disabled>
                            </div>
                        </div>

                    @endif

                    @if($temp1 != 'Elegir')

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Descripcion de la deuda</label>
                                <select wire:model="temp2" class="form-control">
                                    <option value="Elegir">Elegir</option>
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->description}}</option>
                                    @endforeach
                                </select>
                                @error('temp2')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break

                @case('deposito/retiro')

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Cuenta Bancaria</label>
                            <select wire:model="bankAccountId" class="form-control">
                                <option value="Elegir">Elegir</option>
                                @foreach ($allBankAccounts as $bank_account)
                                    <option value="{{$bank_account->id}}">{{$bank_account->bank->description}} || {{$bank_account->type}} || {{$bank_account->currency}} || {{$bank_account->company->description}}</option>
                                @endforeach
                            </select>
                            @error('bankAccountId')
                                <span class="text-danger er">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if($bankAccountId != 'Elegir')

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" wire:model="bankAccountBalance" class="form-control" disabled>
                                @error('bankAccountBalance')
                                    <span class="text-danger er">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    @endif

                @break
                    
            @endswitch

            @if($type != 'Elegir')

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Monto de Ingreso</label>
                        <input type="text" wire:model.lazy="income_amount" class="form-control" placeholder="0.00">
                        @error('income_amount')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            @endif

        @break
            
    @endswitch

</div>

@include('common.modalFooter')