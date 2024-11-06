<?php

namespace App\Http\Livewire;

use App\Models\Anticretic;
use App\Models\Appropriation;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\CheckReceivable;
use App\Models\Company;
use App\Models\Costumer;
use App\Models\CostumerReceivable;
use App\Models\Cover;
use App\Models\Detail;
use App\Models\Gym;
use App\Models\Import;
use App\Models\OtherProvider;
use App\Models\OtherReceivable;
use App\Models\Payable;
use App\Models\Paydesk;
use App\Models\Provider;
use App\Models\ProviderPayable;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Paydesks extends Component
{
    use WithPagination;

    public $pageTitle,$componentName,$reportRange,$reportType,$my_total,$i_total,$e_total;
    public $now,$from,$to,$dateFrom,$dateTo,$transaction_types,$details_2,$gen,$gen_det;
    public $selected_id,$description,$action,$type,$income_amount,$discharge_amount;
    public $allBanks,$bankId,$allBankAccounts,$bankAccountId,$bankAccountBalance;
    public $chc1,$chc2,$chc3;
    public $temp,$temp1,$temp2,$temp3,$temp4,$details,$balance;
    private $pagination = 40;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'listado';
        $this->componentName = 'caja general';
        $this->reportRange = 0;
        $this->reportType = 0;
        $this->my_total = 0;
        $this->i_total = 0;
        $this->e_total = 0;
        $this->now = Carbon::now();
        $this->from = $this->now->format('Y-m-d') . ' 00:00:00';
        $this->to = $this->now->format('Y-m-d') . ' 23:59:59';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->transaction_types = [
            ['name' => 'gastos de importacion', 'alias' => 'Mercaderia en transito'],
            ['name' => 'Ventas', 'alias' => 'Ventas'],
            ['name' => 'caja general', 'alias' => 'Variados'],
            ['name' => 'clientes por cobrar', 'alias' => 'Clientes por Cobrar'],
            ['name' => 'cheques por cobrar', 'alias' => 'Cheques por cobrar'],
            ['name' => 'otros por cobrar', 'alias' => 'Otros por Cobrar'],
            ['name' => 'deposito/retiro', 'alias' => 'Depositos/Retiros'],
            ['name' => 'proveedores por pagar', 'alias' => 'Proveedores por Pagar'],
            ['name' => 'consignaciones', 'alias' => 'Consignaciones'],
            ['name' => 'otros por pagar', 'alias' => 'Otros por Pagar'],
            ['name' => 'anticreticos', 'alias' => 'Anticreticos'],
            ['name' => 'facturas/impuestos', 'alias' => 'Facturas/Impuestos'],
            ['name' => 'otros proveedores', 'alias' => 'Otros Proveedores'],
            ['name' => 'gimnasio', 'alias' => 'Gimnasio'],
            ['name' => 'utilidad', 'alias' => 'Utilidad'],
            ['name' => 'cambio de llantas', 'alias' => 'Cambio de LLantas'],
            ['name' => 'diferencia por t/c', 'alias' => 'Diferencia por T/C'],
            ['name' => 'comisiones', 'alias' => 'Comisiones'],
            ['name' => 'perdida por devolucion', 'alias' => 'Perdida por Devolucion'],
            ['name' => 'gastos importadora', 'alias' => 'Gastos Reales'],
            ['name' => 'gastos gorky', 'alias' => 'Gastos Gorky'],
            ['name' => 'gastos construccion', 'alias' => 'Gastos Construccion']
        ];
        $this->details_2 = null;
        $this->gen = Cover::firstWhere('description',$this->componentName);
        $this->gen_det = $this->gen->details->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->selected_id = 0;
        $this->description = '';
        $this->action = 'Elegir';
        $this->type = 'Elegir';
        $this->income_amount = '';
        $this->discharge_amount = '';
        $this->bankId = 'Elegir';
        $this->allBanks = Bank::orderBy('description')->get();
        $this->allBankAccounts = BankAccount::with('company','bank')->get();
        $this->bankAccountId = 'Elegir';
        $this->bankAccountBalance = 0;
        $this->temp = 'Elegir';
        $this->temp1 = 'Elegir';
        $this->temp2 = 'Elegir';
        $this->temp3 = '';
        $this->temp4 = '';
        $this->details = [];
        $this->balance = 0;
        $this->chc1 = 'Elegir';
        $this->chc2 = 'Elegir';
        $this->chc3 = '';

    }

    public function render()
    {
        $this->ReportsByDate();
        
        return view('livewire.paydesk.paydesks',
        [

            'providers' => Provider::with('payables')->orderBy('description','asc')->get(),
            'imports' => Import::where('amount','>',0)->orderBy('id','asc')->get(),
            'checks' => CheckReceivable::orderBy('id','asc')->get(),
            'others' => OtherReceivable::orderby('reference','asc')->where('amount', '>', 0)->get(),
            'antics' => Anticretic::orderby('id','asc')->get(),
            'pays' => Payable::orderby('reference','asc')->get(),
            'payables' => Payable::orderby('reference','asc')->where('amount', '>', 0)->get(),
            'bills' => Bill::where('type','normal')->where('amount','>',0)->orderby('reference','asc')->get(),
            'appropiations' => Appropriation::orderby('id','asc')->get(),
            'other_providers' => OtherProvider::where('amount','>',0)->orderby('reference','asc')->get(),
            'gyms' => Gym::orderby('id','asc')->get(),
            'clients' => Costumer::orderBy('description','asc')->get(),
            'c_clients' => Costumer::with('checks')->whereHas('checks')->orderBy('description','asc')->get(),
            'd_clients' => Costumer::with('debts')->whereHas('debts')->orderBy('description','asc')->get(),
            'banks' => Bank::orderBy('id','asc')->get(),
            'companies' => Company::orderBy('id','asc')->get(),
            'covers' => Cover::orderBy('id','asc')
            ->where([['type','utilidad_diaria'],['description','<>','utilidad bruta del dia']])
            ->orWhere([['type','gasto_diario'],['description','<>','facturas 6% del dia']])->get(),

        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function ReportsByDate()
    {
        if ($this->reportRange == 0) {

            $fecha1 = $this->from;
            $fecha2 = $this->to;
            $this->my_total = $this->gen->balance;

        } elseif ($this->reportRange == 1) {

            if ($this->dateFrom == '' || $this->dateTo == '') {

                $this->emit('error-message', 'Seleccione fecha de inicio y fecha de fin.');
                return;

            } else {

                $fecha1 = $this->dateFrom . ' 00:00:00';
                $fecha2 = $this->dateTo . ' 23:59:59';

                if ($this->gen->details->whereBetween('created_at',[$fecha1, $fecha2])->last()) {

                    $this->my_total = $this->gen->details->whereBetween('created_at',[$fecha1, $fecha2])->last()->actual_balance;
    
                } else {
    
                    $this->my_total = 0;
    
                }
            }
        }

        if ($this->reportType == 0) {

            $this->details_2 = Paydesk::orderBy('action')->whereBetween('created_at', [$fecha1, $fecha2])->get();
            
        } else {
            
            $this->details_2 = Paydesk::orderBy('id')->whereBetween('created_at', [$fecha1, $fecha2])->where('type',$this->reportType)->get();

        }

        $this->i_total = $this->details_2->where('action','ingreso')->sum('amount');
        $this->e_total = $this->details_2->where('action','egreso')->sum('amount');

    }

    public function updatedbankAccountId($bank_account_id)
    {
        if (!$this->allBankAccounts->find($bank_account_id)) {

            $this->bankAccountBalance = 0;

        } else {

            $this->bankAccountBalance = floatval($this->allBankAccounts->find($bank_account_id)->amount);

        }
    }

    public function updatedchc1($id){

        $this->details = CheckReceivable::where('costumer_id',$id)->get();
    }

    public function updatedchc2($id){

        if(CheckReceivable::firstWhere('id',$id) != null){

            $this->balance = floatval(CheckReceivable::firstWhere('id',$id)->amount);
            $this->temp3 = CheckReceivable::firstWhere('id',$id)->number;
            $this->temp = Bank::firstWhere('id',CheckReceivable::firstWhere('id',$id)->bank_id)->description;

        }else{

            $this->balance = 0;
            $this->temp3 = '';
            $this->temp = 'Elegir';
        }
    }

    public function updatedtemp1($id)
    {
        switch($this->type){

            case 'anticreticos':

                $this->details = Anticretic::where('id',$id)->get();

            break;

            case 'otros por pagar':

                if(Payable::firstWhere('id',$id) != null){

                    $this->balance = floatval(Payable::firstWhere('id',$id)->amount);
                    $this->temp2 = Payable::firstWhere('id',$id)->description;
        
                }else{
        
                    $this->balance = 0;
                    $this->temp3 = '';
                }

            break;

            case 'otros por cobrar':

                $this->details = OtherReceivable::where('id',$id)->get();

            break;

            case 'clientes por cobrar':

                $this->details = CostumerReceivable::where('costumer_id',$id)->where('amount', '>', 0)->get();

            break;

            case 'proveedores por pagar':

                $this->details = ProviderPayable::where('provider_id',$id)->where('amount', '>', 0)->get();

            break;

            case 'consignaciones':

                $this->details = Appropriation::where('id',$id)->get();

            break;

            case 'otros proveedores':

                if(OtherProvider::firstWhere('id',$id) != null){

                    $this->balance = floatval(OtherProvider::firstWhere('id',$id)->amount);
                    $this->temp2 = OtherProvider::firstWhere('id',$id)->description;
        
                }else{
        
                    $this->balance = 0;
                    $this->temp3 = '';
                }

            break;

            case 'facturas/impuestos':

                if(Bill::firstWhere('id',$id) != null){

                    $this->balance = floatval(Bill::firstWhere('id',$id)->amount);
                    $this->temp2 = Bill::firstWhere('id',$id)->description;
        
                }else{
        
                    $this->balance = 0;
                    $this->temp3 = '';
                }

            break;

            case 'gastos de importacion':

                if($this->action == 'ingreso' && $this->temp1 != 'Elegir'){

                    $this->balance = floatval(Import::firstWhere('id',$id)->amount);
                }

            break;

        }
    }

    public function updatedtemp2($id)
    {
        switch($this->type){

            case 'anticreticos':

                if(Anticretic::firstWhere('id',$id) != null){

                    $this->balance = floatval(Anticretic::firstWhere('id',$id)->amount);
        
                }else{
        
                    $this->balance = 0;
                }

            break;

            case 'otros por cobrar':

                if(OtherReceivable::firstWhere('id',$id) != null){

                    $this->balance = floatval(OtherReceivable::firstWhere('id',$id)->amount);
        
                }else{
        
                    $this->balance = 0;
                }

            break;

            case 'clientes por cobrar':

                if(CostumerReceivable::firstWhere('id',$id) != null){

                    $this->balance = floatval(CostumerReceivable::firstWhere('id',$id)->amount);
        
                }else{
        
                    $this->balance = 0;
                }

            break;

            case 'proveedores por pagar':

                if(ProviderPayable::firstWhere('id',$id) != null){

                    $this->balance = floatval(ProviderPayable::firstWhere('id',$id)->amount);
        
                }else{
        
                    $this->balance = 0;
                }

            break;

            case 'consignaciones':

                if(Appropriation::firstWhere('id',$id) != null){

                    $this->balance = floatval(Appropriation::firstWhere('id',$id)->amount);
        
                }else{
        
                    $this->balance = 0;
                }

            break;

        }
    }

    public function Store()
    {
        if (!$this->gen_det) {

            $this->emit('error-message','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [

                'description' => 'required|min:10|max:255',
                'action' => 'not_in:Elegir',
                'type' => 'exclude_if:action,Elegir|not_in:Elegir',
                'income_amount' => 'exclude_unless:action,ingreso|exclude_if:type,Elegir|required|numeric|gt:0',
                'discharge_amount' => "exclude_unless:action,egreso|exclude_if:type,Elegir|required|numeric|gt:0|lte:$this->my_total",
                'bankAccountId' => 'exclude_unless:type,deposito/retiro|not_in:Elegir',
                'bankAccountBalance' => 'exclude_unless:type,deposito/retiro|exclude_if:action,egreso|exclude_if:bankAccountId,Elegir|gte:income_amount',
                'chc1' => 'exclude_unless:type,cheques por cobrar|not_in:Elegir',
                'chc2' => 'exclude_unless:type,cheques por cobrar|not_in:Elegir',
                'chc3' => 'exclude_unless:type,cheques por cobrar|exclude_if:action,ingreso|required|numeric',
            ];

            $messages = [

                'description.required' => 'Campo requerido',
                'description.min' => 'Minimo 10 caracteres',
                'description.max' => 'Maximo 255 caracteres',
                'action.not_in' => 'Seleccione una opcion',
                'type.not_in' => 'Seleccione una opcion',
                'income_amount.required' => 'Campo requerido',
                'income_amount.numeric' => 'Este campo solo admite numeros',
                'income_amount.gt' => 'El monto de ingreso debe ser mayor a 0',
                'discharge_amount.required' => 'Campo requerido',
                'discharge_amount.numeric' => 'Este campo solo admite numeros',
                'discharge_amount.gt' => 'El monto de egreso debe ser mayor a 0',
                'discharge_amount.lte' => 'El monto de egreso debe ser menor o igual al saldo de caja',
                'bankAccountId.not_in' => 'Seleccione una opcion',
                'bankAccountBalance.gte' => 'El saldo debe ser mayor o igual al monto de ingreso',
                'chc1.not_in' => 'Seleccione una opcion',
                'chc2.not_in' => 'Seleccione una opcion',
                'chc3.required' => 'Campo requerido',
                'chc3.numeric' => 'Este campo solo admite numeros',
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
            try {

                if ($this->action == 'ingreso') {

                    $paydesk = Paydesk::create([

                        'action' => $this->action,
                        'description' => $this->description,
                        'type' => $this->type,
                        'relation' => 0,
                        'amount' => $this->income_amount

                    ]);

                    $this->gen->update([

                        'balance' => $this->gen->balance + $paydesk->amount

                    ]);

                    $this->gen_det->update([

                        'ingress' => $this->gen_det->ingress + $paydesk->amount,
                        'actual_balance' => $this->gen_det->actual_balance + $paydesk->amount

                    ]);

                } else {

                    $paydesk = Paydesk::create([

                        'action' => $this->action,
                        'description' => $this->description,
                        'type' => $this->type,
                        'relation' => 0,
                        'amount' => $this->discharge_amount
                    ]);

                    $this->gen->update([

                        'balance' => $this->gen->balance - $paydesk->amount

                    ]);

                    $this->gen_det->update([

                        'egress' => $this->gen_det->egress + $paydesk->amount,
                        'actual_balance' => $this->gen_det->actual_balance - $paydesk->amount

                    ]);

                }

                if (!$paydesk) {

                    $this->emit('error-message', 'Error al registrar movimiento de caja.');
                    return;

                } else {

                    if (Cover::firstWhere('description',$paydesk->type) != null) {

                        $cov = Cover::firstWhere('description',$paydesk->type);
                        $cov_det = $cov->details->where('cover_id',$cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();

                        switch ($paydesk->type) {

                            case 'gastos de importacion':

                                if ($paydesk->action == 'ingreso') {

                                    $debt = Import::find($this->temp1);

                                    if ($paydesk->amount > $debt->amount) {

                                        $this->emit('movement-error','El pago es mayor a la deuda.');
                                        return;

                                    } else {

                                        if ($this->temp1 == 'Elegir') {

                                            $this->emit('movement-error','Seleccione todas las opciones.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
            
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if($detail){

                                                $debt->update([
                
                                                    'amount' => $debt->amount - $detail->amount
                            
                                                ]);
                            
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                        
                                                $cov_det->update([
        
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);
        
                                                $paydesk->update([
        
                                                    'relation' => $detail->id
                                    
                                                ]);
                                            }
                                        }
                                    }

                                } else {
                                    
                                    $debt = Import::create([
            
                                        'description' => $paydesk->description,
                                        'amount' => $paydesk->amount
                
                                    ]);
                
                                    $cov->update([
                            
                                        'balance' => $cov->balance + $debt->amount
                        
                                    ]);
                
                                    $cov_det->update([
            
                                        'ingress' => $cov_det->ingress + $debt->amount,
                                        'actual_balance' => $cov_det->actual_balance + $debt->amount
                        
                                    ]);

                                    $paydesk->update([

                                        'relation' => $debt->id
                        
                                    ]);
                                    
                                }
            
                            break;

                            case 'clientes por cobrar': 
            
                                if ($paydesk->action == 'ingreso') {

                                    $debt = CostumerReceivable::find($this->temp2);
            
                                    if ($paydesk->amount > $debt->amount) {

                                        $this->emit('movement-error','El pago es mayor a la deuda.');
                                        return;

                                    } else {

                                        if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                            $this->emit('movement-error','Seleccione todas las opciones.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
            
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                
                                                    'amount' => $debt->amount - $detail->amount
                            
                                                ]);

                                                $cov->update([
                            
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                        
                                                $cov_det->update([
        
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);
        
                                                $paydesk->update([
        
                                                    'relation' => $detail->id
                                    
                                                ]);

                                            }
                                        }
                                    }
            
                                } else {

                                    if ($this->temp == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $costumer = Costumer::find($this->temp);
            
                                        $debt = CostumerReceivable::create([
                
                                            'description' => $paydesk->description,
                                            'amount' => $paydesk->amount,
                                            'costumer_id' => $costumer->id
                
                                        ]);
                
                                        $cov->update([
                            
                                            'balance' => $cov->balance + $debt->amount
                            
                                        ]);
                
                                        $cov_det->update([

                                            'ingress' => $cov_det->ingress + $debt->amount,
                                            'actual_balance' => $cov_det->actual_balance + $debt->amount
                            
                                        ]);

                                        $paydesk->update([

                                            'relation' => $debt->id
                            
                                        ]);

                                    }
                                }
            
                            break;
            
                            case 'cheques por cobrar': 
            
                                if ($paydesk->action == 'ingreso') {

                                    $debt = CheckReceivable::find($this->chc2);
            
                                    if ($paydesk->amount > $debt->amount) {

                                        $this->emit('movement-error','El monto es mayor al saldo.');
                                        return;

                                    } else {

                                        $detail = $debt->details()->create([
            
                                            'description' => $paydesk->description,
                                            'amount' => $paydesk->amount,
                                            'previus_balance' => $debt->amount,
                                            'actual_balance' => $debt->amount - $paydesk->amount
                                            
                                        ]);

                                        if ($detail) {

                                            $debt = $debt->update([
            
                                                'amount' => $debt->amount - $detail->amount
                    
                                            ]);
    
                                            $cov->update([
                                
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                                
                                            $cov_det->update([
                            
                                                'egress' => $cov_det->egress + $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);
    
                                            $paydesk->update([
    
                                                'relation' => $detail->id
                                
                                            ]);
                                        }
                                    }
            
                                } else {
            
                                    $costumer = Costumer::find($this->chc1);
                                    $bank = Bank::find($this->chc2);
            
                                    $debt = CheckReceivable::create([
            
                                        'description' => $paydesk->description,
                                        'amount' => $paydesk->amount,
                                        'number' => $this->chc3,
                                        'bank_id' => $bank->id,
                                        'costumer_id' => $costumer->id
            
                                    ]);
            
                                    $cov->update([
                        
                                        'balance' => $cov->balance + $debt->amount
                        
                                    ]);
                        
                                    $cov_det->update([
                    
                                        'ingress' => $cov_det->ingress + $debt->amount,
                                        'actual_balance' => $cov_det->actual_balance + $debt->amount
                        
                                    ]);

                                    $paydesk->update([

                                        'relation' => $debt->id
                        
                                    ]);

                                }
            
                            break;
            
                            case 'otros por cobrar': 
            
                                if ($paydesk->action == 'ingreso') {

                                    $debt = OtherReceivable::find($this->temp1);
            
                                    if ($paydesk->amount > $debt->amount) {

                                        $this->emit('movement-error','El pago es mayor a la deuda.');
                                        return;

                                    } else {

                                        if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                            $this->emit('movement-error','Seleccione todas las opciones.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
            
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                
                                                    'amount' => $debt->amount - $detail->amount
                        
                                                ]);
                        
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $cov_det->update([
                                
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);
        
                                                $paydesk->update([
        
                                                    'relation' => $detail->id
                                    
                                                ]);

                                            }
                                        }
                                    }
            
                                } else {

                                    if ($this->temp3 == '' || $this->temp3 == null) {

                                        $this->emit('movement-error','Ingrese la referencia.');
                                        return;

                                    } else {

                                        $debt = OtherReceivable::create([
            
                                            'description' => $paydesk->description,
                                            'reference' => $this->temp3,
                                            'amount' => $paydesk->amount
                    
                                        ]);
                
                                        $cov->update([
                            
                                            'balance' => $cov->balance + $debt->amount
                            
                                        ]);
                            
                                        $cov_det->update([
                        
                                            'ingress' => $cov_det->ingress + $debt->amount,
                                            'actual_balance' => $cov_det->actual_balance + $debt->amount
                            
                                        ]);

                                        $paydesk->update([

                                            'relation' => $debt->id
                            
                                        ]);

                                    }
                                }
            
                            break;
            
                            case 'proveedores por pagar': 
                                
                                $debt = ProviderPayable::find($this->temp2);

                                if ($paydesk->amount > $debt->amount) {

                                    $this->emit('movement-error','El pago es mayor a la deuda.');
                                    return;

                                } else {

                                    if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $detail = $debt->details()->create([
                
                                            'description' => $paydesk->description,
                                            'amount' => $paydesk->amount,
                                            'previus_balance' => $debt->amount,
                                            'actual_balance' => $debt->amount - $paydesk->amount
                                        ]);
                    
                                        if ($detail) {

                                            $debt->update([
                
                                                'amount' => $debt->amount - $detail->amount
                        
                                            ]);
                        
                                            $cov->update([
                                    
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
    
                                                'egress' => $cov_det->egress + $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);
    
                                            $paydesk->update([
    
                                                'relation' => $detail->id
                                
                                            ]);

                                        }
                                    }
                                }
            
                            break;
            
                            case 'consignaciones': 
                                
                                $debt = Appropriation::find($this->temp2);

                                if ($paydesk->amount > $debt->amount) {

                                    $this->emit('movement-error','El pago es mayor a la deuda.');
                                    return;

                                } else {

                                    if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $detail = $debt->details()->create([
                
                                            'description' => $paydesk->description,
                                            'amount' => $paydesk->amount,
                                            'previus_balance' => $debt->amount,
                                            'actual_balance' => $debt->amount - $paydesk->amount
                                        ]);
                    
                                        if ($detail) {

                                            $debt->update([
                
                                                'amount' => $debt->amount - $detail->amount
                        
                                            ]);
                        
                                            $cov->update([
                                    
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
    
                                                'egress' => $cov_det->egress + $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);
    
                                            $paydesk->update([
    
                                                'relation' => $detail->id
                                
                                            ]);

                                        }
                                    }
                                }
            
                            break;

                            case 'otros por pagar': 
            
                                if ($paydesk->action == 'ingreso') {
                                    
                                    /*HACER ESTO EN LAS VALIDACIONES*/
                                    if ($this->temp != 'Elegir') {
            
                                        if ($this->temp == 'Nueva') {

                                            /*HACER ESTO EN LAS VALIDACIONES*/
                                            if ($this->temp3 == '' || $this->temp3 == null) {

                                                $this->emit('movement-error','Ingrese la referencia.');
                                                return;

                                            } else {

                                                $debt = Payable::create([
                
                                                    'description' => $paydesk->description,
                                                    'reference' => $this->temp3,
                                                    'amount' => $paydesk->amount
                            
                                                ]);

                                                $detail = $debt->details()->create([
                
                                                    'description' => $paydesk->description,
                                                    'amount' => $debt->amount,
                                                    'previus_balance' => 0,
                                                    'actual_balance' => $debt->amount
                                                ]);

                                            }
                
                                        } else {

                                            if ($this->temp1 == 'Elegir') {

                                                $this->emit('movement-error','Seleccione todas las opciones.');
                                                return;

                                            } else {

                                                $debt = Payable::find($this->temp1);
                
                                                $detail = $debt->details()->create([
                    
                                                    'description' => $paydesk->description,
                                                    'amount' => $paydesk->amount,
                                                    'previus_balance' => $debt->amount,
                                                    'actual_balance' => $debt->amount + $paydesk->amount
                                                ]);
                    
                                                $debt->update([
                                
                                                    'amount' => $debt->amount + $detail->amount
                                        
                                                ]);

                                            }
                                        }

                                        $paydesk->update([

                                            'relation' => $detail->id
                            
                                        ]);

                                        $cov->update([
                        
                                            'balance' => $cov->balance + $detail->amount
                            
                                        ]);
                            
                                        $cov_det->update([
                        
                                            'ingress' => $cov_det->ingress + $detail->amount,
                                            'actual_balance' => $cov_det->actual_balance + $detail->amount
                            
                                        ]);

                                    } else {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    }
            
                                } else {

                                    if ($this->temp1 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $debt = Payable::find($this->temp1);

                                        if ($paydesk->amount > $debt->amount) {

                                            $this->emit('movement-error','El pago es mayor a la deuda.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
                    
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                        
                                                ]);

                                                $paydesk->update([
    
                                                    'relation' => $detail->id
                                    
                                                ]);
                        
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $cov_det->update([
                                
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);

                                            }
                                        }
                                    }
                                }
            
                            break;

                            case 'anticreticos': 
            
                                if ($paydesk->action == 'ingreso') {
                                    
                                    if ($this->temp != 'Elegir') {
            
                                        if ($this->temp == 'Nuevo') {

                                            if ($this->temp3 == '' || $this->temp3 == null) {

                                                $this->emit('movement-error','Ingrese la referencia.');
                                                return;

                                            } else {

                                                $debt = Anticretic::create([
                
                                                    'description' => $paydesk->description,
                                                    'reference' => $this->temp3,
                                                    'amount' => $paydesk->amount
                            
                                                ]);

                                                $detail = $debt->details()->create([
                
                                                    'description' => $paydesk->description,
                                                    'amount' => $debt->amount,
                                                    'previus_balance' => 0,
                                                    'actual_balance' => $debt->amount
                                                ]);

                                            }
                
                                        } else {

                                            if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                                $this->emit('movement-error','Seleccione todas las opciones.');
                                                return;

                                            } else {

                                                $debt = Anticretic::find($this->temp1);
                
                                                $detail = $debt->details()->create([
                    
                                                    'description' => $paydesk->description,
                                                    'amount' => $paydesk->amount,
                                                    'previus_balance' => $debt->amount,
                                                    'actual_balance' => $debt->amount + $paydesk->amount
                                                ]);
                    
                                                $debt->update([
                                
                                                    'amount' => $debt->amount + $detail->amount
                                        
                                                ]);

                                            }
                                        }

                                        $paydesk->update([

                                            'relation' => $detail->id
                            
                                        ]);

                                        $cov->update([
                        
                                            'balance' => $cov->balance + $detail->amount
                            
                                        ]);
                            
                                        $cov_det->update([
                        
                                            'ingress' => $cov_det->ingress + $detail->amount,
                                            'actual_balance' => $cov_det->actual_balance + $detail->amount
                            
                                        ]);

                                    } else {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    }
            
                                } else {

                                    if ($this->temp1 == 'Elegir' || $this->temp2 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $debt = Anticretic::find($this->temp1);

                                        if ($paydesk->amount > $debt->amount) {

                                            $this->emit('movement-error','El pago es mayor a la deuda.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
            
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                        
                                                ]);

                                                $paydesk->update([
    
                                                    'relation' => $detail->id
                                    
                                                ]);
                        
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $cov_det->update([
                                
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);

                                            }
                                        }
                                    }
                                }
            
                            break;

                            case 'facturas/impuestos': 
            
                                if ($paydesk->action == 'ingreso') {
                                    
                                    if ($this->temp != 'Elegir') {
            
                                        if ($this->temp == 'Nueva') {

                                            if (($this->temp3 == '' || $this->temp3 == null) || $this->temp4 == '' || $this->temp4 == null) {

                                                $this->emit('movement-error','Rellene todos los campos.');
                                                return;

                                            } else {

                                                $debt = Bill::create([
                
                                                    'description' => $paydesk->description,
                                                    'reference' => $this->temp3,
                                                    'amount' => $paydesk->amount,
                                                    'type' => 'normal'
                            
                                                ]);

                                                $detail = $debt->details()->create([
                
                                                    'description' => $this->temp4,
                                                    'amount' => $debt->amount,
                                                    'previus_balance' => 0,
                                                    'actual_balance' => $debt->amount
                                                ]);

                                            }
                
                                        } else {

                                            if ($this->temp1 == 'Elegir') {

                                                $this->emit('movement-error','Seleccione todas las opciones.');
                                                return;

                                            } else {

                                                $debt = Bill::find($this->temp1);
                
                                                $detail = $debt->details()->create([
                    
                                                    'description' => $paydesk->description,
                                                    'amount' => $paydesk->amount,
                                                    'previus_balance' => $debt->amount,
                                                    'actual_balance' => $debt->amount + $paydesk->amount
                                                ]);
                    
                                                $debt->update([
                                
                                                    'amount' => $debt->amount + $detail->amount
                                        
                                                ]);

                                            }
                                        }

                                        $paydesk->update([

                                            'relation' => $detail->id
                            
                                        ]);

                                        $cov->update([
                        
                                            'balance' => $cov->balance + $detail->amount
                            
                                        ]);
                            
                                        $cov_det->update([
                        
                                            'ingress' => $cov_det->ingress + $detail->amount,
                                            'actual_balance' => $cov_det->actual_balance + $detail->amount
                            
                                        ]);

                                    } else {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    }
            
                                } else {

                                    if ($this->temp1 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $debt = Bill::find($this->temp1);

                                        if ($paydesk->amount > $debt->amount) {

                                            $this->emit('movement-error','El pago es mayor a la deuda.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
                    
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                        
                                                ]);

                                                $paydesk->update([
    
                                                    'relation' => $detail->id
                                    
                                                ]);
                        
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $cov_det->update([
                                
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);

                                            }
                                        }
                                    }
                                }
            
                            break;
            
                            case 'otros proveedores': 
            
                                if ($paydesk->action == 'ingreso') {
                                    
                                    if ($this->temp != 'Elegir') {
            
                                        if ($this->temp == 'Nueva') {

                                            if (($this->temp3 == '' || $this->temp3 == null) || $this->temp4 == '' || $this->temp4 == null) {

                                                $this->emit('movement-error','Rellene todos los campos.');
                                                return;

                                            } else {

                                                $debt = OtherProvider::create([
                
                                                    'description' => $paydesk->description,
                                                    'reference' => $this->temp3,
                                                    'amount' => $paydesk->amount
                            
                                                ]);

                                                $detail = $debt->details()->create([
                
                                                    'description' => $this->temp4,
                                                    'amount' => $debt->amount,
                                                    'previus_balance' => 0,
                                                    'actual_balance' => $debt->amount
                                                ]);

                                            }
                
                                        } else {

                                            if ($this->temp1 == 'Elegir' || ($this->temp4 == '' || $this->temp4 == null) ) {

                                                $this->emit('movement-error','Seleccione todas las opciones y rellene todos los campos.');
                                                return;

                                            } else {

                                                $debt = OtherProvider::find($this->temp1);
                
                                                $detail = $debt->details()->create([
                    
                                                    'description' => $this->temp4,
                                                    'amount' => $paydesk->amount,
                                                    'previus_balance' => $debt->amount,
                                                    'actual_balance' => $debt->amount + $paydesk->amount
                                                ]);
                    
                                                $debt->update([
                                
                                                    'amount' => $debt->amount + $detail->amount
                                        
                                                ]);

                                            }
                                        }

                                        $paydesk->update([

                                            'relation' => $detail->id
                            
                                        ]);

                                        $cov->update([
                        
                                            'balance' => $cov->balance + $detail->amount
                            
                                        ]);
                            
                                        $cov_det->update([
                        
                                            'ingress' => $cov_det->ingress + $detail->amount,
                                            'actual_balance' => $cov_det->actual_balance + $detail->amount
                            
                                        ]);

                                    } else {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    }
            
                                } else {

                                    if ($this->temp1 == 'Elegir') {

                                        $this->emit('movement-error','Seleccione todas las opciones.');
                                        return;

                                    } else {

                                        $debt = OtherProvider::find($this->temp1);

                                        if ($paydesk->amount > $debt->amount) {

                                            $this->emit('movement-error','El pago es mayor a la deuda.');
                                            return;

                                        } else {

                                            $detail = $debt->details()->create([
                    
                                                'description' => $paydesk->description,
                                                'amount' => $paydesk->amount,
                                                'previus_balance' => $debt->amount,
                                                'actual_balance' => $debt->amount - $paydesk->amount
                                            ]);

                                            if ($detail) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                        
                                                ]);

                                                $paydesk->update([
    
                                                    'relation' => $detail->id
                                    
                                                ]);
                        
                                                $cov->update([
                                    
                                                    'balance' => $cov->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $cov_det->update([
                                
                                                    'egress' => $cov_det->egress + $detail->amount,
                                                    'actual_balance' => $cov_det->actual_balance - $detail->amount
                                    
                                                ]);

                                            }
                                        }
                                    }
                                }
            
                            break;

                            case 'gimnasio':

                                if ($paydesk->action == 'ingreso') {

                                    $debt = Gym::create([
                                        
                                        'description' => $paydesk->description,
                                        'amount' => $paydesk->amount
                
                                    ]);

                                    $cov->update([
                
                                        'balance' => $cov->balance + $debt->amount
                        
                                    ]);
                        
                                    $cov_det->update([
                    
                                        'ingress' => $cov_det->ingress + $debt->amount,
                                        'actual_balance' => $cov_det->actual_balance + $debt->amount
                        
                                    ]);

                                    $paydesk->update([

                                        'relation' => $debt->id
                        
                                    ]);

                                } else {

                                    $debt = Gym::create([
                                        
                                        'description' => $paydesk->description,
                                        'amount' => - $paydesk->amount
                
                                    ]);

                                    $cov->update([
                
                                        'balance' => $cov->balance + $debt->amount
                        
                                    ]);
                        
                                    $cov_det->update([
                    
                                        'egress' => $cov_det->egress - $debt->amount,
                                        'actual_balance' => $cov_det->actual_balance + $debt->amount
                        
                                    ]);

                                    $paydesk->update([

                                        'relation' => $debt->id
                        
                                    ]);
                                }

                            break;

                            case 'utilidad': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;

                            case 'cambio de llantas': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;
            
                            case 'diferencia por t/c': 
            
                                if($paydesk->action == 'ingreso'){
            
                                    $cov_det->update([
            
                                        'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                        
                                    ]);
            
                                }else{
            
                                    $cov_det->update([
            
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);
            
                                }
            
                            break;

                            case 'comisiones': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;
            
                            case 'perdida por devolucion': 

                                if ($this->temp3 == '' || $this->temp3 == null) {

                                    $this->emit('movement-error','Rellene todos los campos.');
                                    return;

                                } else {

                                    $cov_det->update([
            
                                        'actual_balance' => $cov_det->actual_balance + $this->temp3
                        
                                    ]);

                                }
            
                            break;
            
                            case 'gastos gorky': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;

                            case 'gastos importadora': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;
            
                            case 'gastos construccion': 
            
                                $cov_det->update([
            
                                    'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                    
                                ]);
            
                            break;

                        }
            
                    } else {
            
                        $bank_account = $this->allBankAccounts->find($this->bankAccountId);
                        $company_name = $bank_account->company->description;
                        $bank_name = $bank_account->bank->description;
                        $bank_account_cover = Cover::firstWhere('description',$bank_name . ' ' . $bank_account->type . ' ' . $bank_account->currency . ' ' . $company_name);
                        $bank_account_cover_detail = $bank_account_cover->details->whereBetween('created_at',[$this->from, $this->to])->first();

                        if ($paydesk->action == 'egreso') {

                            $detail = $bank_account->details()->create([

                                'description' => $paydesk->description,
                                'amount' => $paydesk->amount,
                                'previus_balance' => $bank_account->amount,
                                'actual_balance' => $bank_account->amount + $paydesk->amount

                            ]);

                            if (!$detail) {

                                $this->emit('error-message', 'Error al registrar el detalle del movimiento.');
                                return;

                            } else {

                                $bank_account->update([
                
                                    'amount' => $bank_account->amount + $detail->amount
                    
                                ]);

                                $bank_account_cover->update([
                            
                                    'balance' => $bank_account_cover->balance + $detail->amount
                    
                                ]);

                                $bank_account_cover_detail->update([

                                    'ingress' => $bank_account_cover_detail->ingress + $detail->amount,
                                    'actual_balance' => $bank_account_cover_detail->actual_balance + $detail->amount
                    
                                ]);

                                $paydesk->update([

                                    'relation' => $detail->id
                    
                                ]);

                            }

                        } else {

                            $detail = $bank_account->details()->create([

                                'description' => $paydesk->description,
                                'amount' => $paydesk->amount,
                                'previus_balance' => $bank_account->amount,
                                'actual_balance' => $bank_account->amount - $paydesk->amount

                            ]);

                            if (!$detail) {

                                $this->emit('error-message', 'Error al registrar el detalle del movimiento.');
                                return;

                            } else {

                                $bank_account->update([
                
                                    'amount' => $bank_account->amount - $detail->amount
                    
                                ]);

                                $bank_account_cover->update([
                            
                                    'balance' => $bank_account_cover->balance - $detail->amount
                    
                                ]);

                                $bank_account_cover_detail->update([

                                    'egress' => $bank_account_cover_detail->egress + $detail->amount,
                                    'actual_balance' => $bank_account_cover_detail->actual_balance - $detail->amount
                    
                                ]);

                                $paydesk->update([

                                    'relation' => $detail->id
                    
                                ]);

                            }
                        }
                    }
                }

                DB::commit();
                $this->emit('item-added', 'Registro Exitoso.');
                $this->resetUI();

            } catch (Exception $e) {
                
                DB::rollback();
                $this->emit('error-message', $e->getMessage());
                //$this->emit('error-message', 'Algo salio mal.');
            }
        }
    }

    public function Utility()
    {
        if (!$this->gen_det) {

            $this->emit('error-message','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [

                'income_amount' => 'required|numeric'

            ];

            $messages = [

                'income_amount.required' => 'Campo requerido',
                'income_amount.numeric' => 'Este campo solo admite numeros'

            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
            try {

                $utility_cover = Cover::firstWhere('description','utilidad');
                $utility_cover_detail = $utility_cover->details->whereBetween('created_at',[$this->from, $this->to])->first();

                $utility_cover_detail->update([

                    'actual_balance' => $utility_cover_detail->actual_balance + $this->income_amount

                ]);

                DB::commit();
                $this->emit('item-updated', 'Registro Exitoso.');
                $this->resetUI();

            } catch (Exception $e) {
                
                DB::rollback();
                //$this->emit('error-message', $e->getMessage());
                $this->emit('error-message', 'Algo salio mal.');

            }
        }
    }

    public function Collect(){

        if($this->gen_det != null){

            $data = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->from, $this->to])->where('type','Ventas')->get();

            if(count($data) == 0){

                $sales = Sale::with('product')->whereBetween('created_at',[$this->from,$this->to])->where('state_id',8)->get();

                if(count($sales) > 0){

                    DB::beginTransaction();
            
                        try {

                            $total_cost = 0;

                            foreach($sales as $sale){

                                $total_cost += $sale->quantity * $sale->product->cost;
                            }
                            
                            $ud = Cover::firstWhere('description','utilidad bruta del dia');
                            $ud_det = $ud->details->where('cover_id',$ud->id)->whereBetween('created_at',[$this->from, $this->to])->first();
                            $sa = Cover::firstWhere('description','inventario');
                            $sa_det = $sa->details->where('cover_id',$sa->id)->whereBetween('created_at',[$this->from, $this->to])->first();
                            
                            Paydesk::create([
                
                                'action' => 'ingreso',
                                'description' => 'Ventas del dia',
                                'type' => 'Ventas',
                                'relation' => 0,
                                'amount' => $sales->sum('total')
                            ]);
                            
                            $this->gen->update([
                
                                'balance' => $this->gen->balance + $sales->sum('total')
                
                            ]);
                
                            $this->gen_det->update([
                
                                'ingress' => $this->gen_det->ingress + $sales->sum('total'),
                                'actual_balance' => $this->gen_det->actual_balance + $sales->sum('total')
                
                            ]);
                
                            $ud_det->update([
                
                                'actual_balance' => $ud_det->actual_balance + $sales->sum('utility')
                
                            ]);
                
                            $sa->update([
                
                                'balance' => $sa->balance - $total_cost
                
                            ]);
                
                            $sa_det->update([
                
                                'egress' => $sa_det->egress + $total_cost,
                                'actual_balance' => $sa_det->actual_balance - $total_cost
                
                            ]);
                            
                            DB::commit();
                            $this->emit('item-added', 'Registro Exitoso');
                            $this->resetUI();
                            $this->mount();
                            $this->render();

                        } catch (Exception) {
                    
                            DB::rollback();
                            $this->emit('movement-error', 'Algo salio mal');
                        }

                }else{

                    $this->emit('paydesk-error', 'No se han registrado ventas aun');
                    return;
                }

            }else{

                $this->emit('paydesk-error', 'Ya se obtuvieron las ventas del dia');
                return;
            }
        
        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }
    }

    protected $listeners = [

        'destroy' => 'Destroy',
        'collect' => 'Collect'
    ];

    public function Destroy(Paydesk $paydesk){

        if ($this->gen_det != null) {

            DB::beginTransaction();
            
                try {

                    if (Cover::firstWhere('description',$paydesk->type) != null) {
                
                        $cov = Cover::firstWhere('description',$paydesk->type);
                        $cov_det = $cov->details->where('cover_id',$cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
                        
                        if ($paydesk->action == 'ingreso') {

                            switch ($paydesk->type) {

                                case 'gastos de importacion':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = Import::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                                
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'clientes por cobrar':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = CostumerReceivable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                                
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'cheques por cobrar':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = CheckReceivable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                                
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'otros por cobrar':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = OtherReceivable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                                
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'otros por pagar':

                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = Payable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            if (count($debt->details) > 1) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                    
                                                ]);

                                            } else {

                                                $debt->delete();

                                            }

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);

                                            $detail->delete();

                                        }
                                    }

                                break;

                                case 'anticreticos':

                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = Anticretic::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            if (count($debt->details) > 1) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                    
                                                ]);

                                            } else {

                                                $debt->delete();

                                            }

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);

                                            $detail->delete();

                                        }
                                    }

                                break;

                                case 'facturas/impuestos':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = Bill::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            if (count($debt->details) > 1) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                    
                                                ]);

                                            } else {

                                                $debt->delete();

                                            }

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);

                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'otros proveedores':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = OtherProvider::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            if (count($debt->details) > 1) {

                                                $debt->update([
                                
                                                    'amount' => $debt->amount - $detail->amount
                                    
                                                ]);

                                            } else {

                                                $debt->delete();

                                            }

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance - $detail->amount
                                
                                            ]);

                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'gimnasio':
                                    
                                    $debt = Gym::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $cov->update([
                        
                                            'balance' => $cov->balance - $debt->amount
                            
                                        ]);
                    
                                        $cov_det->update([
                    
                                            'ingress' => $cov_det->ingress - $debt->amount,
                                            'actual_balance' => $cov_det->actual_balance - $debt->amount
                            
                                        ]);
    
                                        $debt->delete();

                                    }
                
                                break;

                                case 'utilidad':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                                case 'cambio de llantas':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                                case 'diferencia por t/c':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                            }

                            $this->gen->update([
                        
                                'balance' => $this->gen->balance - $paydesk->amount
                    
                            ]);
                    
                            $this->gen_det->update([
                    
                                'ingress' => $this->gen_det->ingress - $paydesk->amount,
                                'actual_balance' => $this->gen_det->actual_balance - $paydesk->amount
                    
                            ]);
                        
                        } else {

                            switch ($paydesk->type) {

                                case 'gastos de importacion':
                                    
                                    $debt = Import::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        if (count($debt->details) > 0) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $debt->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $debt->amount,
                                                'actual_balance' => $cov_det->actual_balance - $debt->amount
                                
                                            ]);
                    
                                            $debt->delete();

                                        }
                                    }
                
                                break;

                                case 'clientes por cobrar':
                                    
                                    $debt = CostumerReceivable::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        if (count($debt->details) > 0) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $debt->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $debt->amount,
                                                'actual_balance' => $cov_det->actual_balance - $debt->amount
                                
                                            ]);
                    
                                            $debt->delete();

                                        }
                                    }
                
                                break;

                                case 'cheques por cobrar':
                                    
                                    $debt = CheckReceivable::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        if (count($debt->details) > 0) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $debt->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $debt->amount,
                                                'actual_balance' => $cov_det->actual_balance - $debt->amount
                                
                                            ]);
                    
                                            $debt->delete();

                                        }
                                    }
                
                                break;

                                case 'otros por cobrar':
                                    
                                    $debt = OtherReceivable::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        if (count($debt->details) > 0) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $cov->update([
                                
                                                'balance' => $cov->balance - $debt->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'ingress' => $cov_det->ingress - $debt->amount,
                                                'actual_balance' => $cov_det->actual_balance - $debt->amount
                                
                                            ]);
                    
                                            $debt->delete();

                                        }
                                    }
                
                                break;

                                case 'proveedores por pagar':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = ProviderPayable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                            
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'consignaciones':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $debt = Appropriation::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([
                            
                                                'amount' => $debt->amount + $detail->amount
                                
                                            ]);
    
                                            $cov->update([
                                
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
                    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'otros por pagar':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;
                                        
                                    } else {

                                        $debt = Payable::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([

                                                'amount' => $debt->amount + $detail->amount
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
    
                                            $detail->delete();

                                        }
                                    }

                                break;

                                case 'anticreticos':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;
                                        
                                    } else {

                                        $debt = Anticretic::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([

                                                'amount' => $debt->amount + $detail->amount
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'facturas/impuestos':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;
                                        
                                    } else {

                                        $debt = Bill::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([

                                                'amount' => $debt->amount + $detail->amount
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'otros proveedores':
                                    
                                    $detail = Detail::find($paydesk->relation);

                                    if (!$detail) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;
                                        
                                    } else {

                                        $debt = OtherProvider::find($detail->detailable_id);

                                        if ($debt->details->last()->id != $detail->id) {

                                            $this->emit('paydesk-error', 'Se han realizado movimientos posteriores a este registro. Anule esos movimientos primero.');
                                            return;

                                        } else {

                                            $debt->update([

                                                'amount' => $debt->amount + $detail->amount
                                            ]);
    
                                            $cov->update([
                                    
                                                'balance' => $cov->balance + $detail->amount
                                
                                            ]);
                        
                                            $cov_det->update([
                        
                                                'egress' => $cov_det->egress - $detail->amount,
                                                'actual_balance' => $cov_det->actual_balance + $detail->amount
                                
                                            ]);
    
                                            $detail->delete();

                                        }
                                    }
                
                                break;

                                case 'gimnasio':
                                    
                                    $debt = Gym::find($paydesk->relation);

                                    if (!$debt) {

                                        $this->emit('paydesk-error', 'No se ha encontrado el registro.');
                                        return;

                                    } else {

                                        $cov->update([
                        
                                            'balance' => $cov->balance - $debt->amount
                            
                                        ]);
                    
                                        $cov_det->update([
                    
                                            'egress' => $cov_det->egress + $debt->amount,
                                            'actual_balance' => $cov_det->actual_balance - $debt->amount
                            
                                        ]);
    
                                        $debt->delete();

                                    }
                
                                break;

                                case 'diferencia por t/c':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance + $paydesk->amount
                        
                                    ]);

                                break;

                                case 'comisiones':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                                case 'perdida por devolucion':

                                    $cov_det->update([
                    
                                        'actual_balance' => 0
                        
                                    ]);

                                break;

                                case 'gastos gorky':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                                case 'gastos importadora':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                                case 'gastos construccion':

                                    $cov_det->update([
                    
                                        'actual_balance' => $cov_det->actual_balance - $paydesk->amount
                        
                                    ]);

                                break;

                            }

                            $this->gen->update([
                        
                                'balance' => $this->gen->balance + $paydesk->amount
                
                            ]);
                
                            $this->gen_det->update([
                
                                'egress' => $this->gen_det->egress - $paydesk->amount,
                                'actual_balance' => $this->gen_det->actual_balance + $paydesk->amount
                
                            ]);
                        }
                    
                    } else {

                        if ($paydesk->type == 'deposito/retiro') {

                            $detail = Detail::find($paydesk->relation);
                            $bank_account = BankAccount::find($detail->detailable_id);

                            if (!$bank_account) {

                                $this->emit('error-message', 'No se ha encontrado la cuenta bancaria relacionada con este movimiento.');
                                return;

                            } else {

                                $bank_name = Bank::find($bank_account->bank_id)->description;
                                $company_name = Company::find($bank_account->company_id)->description;
                                $bank_account_cover = Cover::firstWhere('description',$bank_name . ' ' . $bank_account->type . ' ' . $bank_account->currency . ' ' . $company_name);

                                if (!$bank_account_cover) {

                                    $this->emit('error-message', 'No se ha encontrado caratula para esta cuenta.');
                                    return;

                                } else {

                                    $bank_account_cover_detail = $bank_account_cover->details->whereBetween('created_at',[$this->from, $this->to])->first();

                                    if (!$bank_account_cover_detail) {

                                        $this->emit('error-message', 'No se ha encontrado caratula del dia para esta cuenta.');
                                        return;

                                    } else {

                                        if ($paydesk->action == 'ingreso') {

                                            if ( ($detail->actual_balance + $detail->amount) != ($bank_account->amount + $detail->amount) ) {

                                                $this->emit('error-message', 'El saldo no coincide. Anule los movimientos mas recientes.');
                                                return;
                
                                            } else {

                                                $bank_account->update([
                                    
                                                    'amount' => $bank_account->amount + $detail->amount
                                    
                                                ]);
                
                                                $bank_account_cover->update([
                                        
                                                    'balance' => $bank_account_cover->balance + $detail->amount
                                    
                                                ]);
                
                                                $bank_account_cover_detail->update([
                
                                                    'egress' => $bank_account_cover_detail->egress - $detail->amount,
                                                    'actual_balance' => $bank_account_cover_detail->actual_balance + $detail->amount
                                    
                                                ]);
                
                                                $this->gen->update([
                                            
                                                    'balance' => $this->gen->balance - $detail->amount
                                    
                                                ]);
                                    
                                                $this->gen_det->update([
                                    
                                                    'ingress' => $this->gen_det->ingress - $detail->amount,
                                                    'actual_balance' => $this->gen_det->actual_balance - $detail->amount
                                    
                                                ]);
                
                                                $detail->delete();

                                            }
            
                                        } else {

                                            if ( ($detail->actual_balance - $detail->amount) != ($bank_account->amount - $detail->amount) ) {

                                                $this->emit('error-message', 'El saldo no coincide. Anule los movimientos mas recientes.');
                                                return;
                
                                            } else {

                                                $bank_account->update([
                                    
                                                    'amount' => $bank_account->amount - $detail->amount
                                    
                                                ]);
                
                                                $bank_account_cover->update([
                                        
                                                    'balance' => $bank_account_cover->balance - $detail->amount
                                    
                                                ]);
                
                                                $bank_account_cover_detail->update([
                
                                                    'ingress' => $bank_account_cover_detail->ingress - $detail->amount,
                                                    'actual_balance' => $bank_account_cover_detail->actual_balance - $detail->amount
                                    
                                                ]);
                
                                                $this->gen->update([
                                            
                                                    'balance' => $this->gen->balance + $detail->amount
                                    
                                                ]);
                                    
                                                $this->gen_det->update([
                                    
                                                    'egress' => $this->gen_det->egress - $detail->amount,
                                                    'actual_balance' => $this->gen_det->actual_balance + $detail->amount
                                    
                                                ]);
                
                                                $detail->delete();

                                            }
                                        }
                                    }
                                }
                            }

                        } else {

                            if($paydesk->type == 'Ventas'){

                                $sales = Sale::with('product')->whereBetween('created_at',[$this->from,$this->to])->where('state_id',8)->get();
                
                                $ud = Cover::firstWhere('description','utilidad bruta del dia');
                                $ud_det = $ud->details->where('cover_id',$ud->id)->whereBetween('created_at',[$this->from, $this->to])->first();
                                $sa = Cover::firstWhere('description','inventario');
                                $sa_det = $sa->details->where('cover_id',$sa->id)->whereBetween('created_at',[$this->from, $this->to])->first();
                
                                $total_cost = 0;
                
                                foreach($sales as $sale){
                
                                    $total_cost += $sale->quantity * $sale->product->cost;
                                }

                                $this->gen->update([
                        
                                    'balance' => $this->gen->balance - $sales->sum('total')
                    
                                ]);
                    
                                $this->gen_det->update([
                    
                                    'ingress' => $this->gen_det->ingress - $sales->sum('total'),
                                    'actual_balance' => $this->gen_det->actual_balance - $sales->sum('total')
                    
                                ]);
                    
                                $ud_det->update([
                    
                                    'actual_balance' => $ud_det->actual_balance - $sales->sum('utility')
                    
                                ]);
                    
                                $sa->update([
                    
                                    'balance' => $sa->balance + $total_cost
                    
                                ]);
                    
                                $sa_det->update([
                    
                                    'egress' => $sa_det->egress - $total_cost,
                                    'actual_balance' => $sa_det->actual_balance + $total_cost
                    
                                ]);
                
                            }else{
                
                                $this->emit('paydesk-error', 'Error desconocido al eliminar');
                                return;
                            }
                        }

                    }
                    
                    $paydesk->delete();
                    DB::commit();
                    $this->emit('item-deleted', 'Registro Eliminado.');
                    $this->resetUI();

                } catch (Exception $e) {
                    
                    DB::rollback();
                    //$this->emit('movement-error', $e->getMessage());
                    $this->emit('movement-error', 'Algo salio mal.');
                }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;
        }

    }

    public function resetUI()
    {
        $this->resetValidation();
        $this->mount();
        $this->render();
    }
}
