<?php

namespace App\Http\Livewire;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Cover;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class BankAccounts extends Component
{
    use WithPagination;

    public $pageTitle,$componentName,$selected_id,$search;
    public $company_id,$bank_id,$type,$currency,$amount,$action,$income_description,$income_amount,$discharge_description,$discharge_amount;
    public $from,$to,$cover_reference,$cover_reference_detail,$bank_account_cover,$bank_account_cover_detail;
    private $pagination = 30;

    public function mount()
    {
        $this->pageTitle = 'Listado';
        $this->componentName = 'Cuentas de Banco';
        $this->selected_id = 0;
        $this->search = '';
        $this->company_id = 'Elegir';
        $this->bank_id = 'Elegir';
        $this->type = 'Elegir';
        $this->currency = 'Elegir';
        $this->amount = '';
        $this->action = 'Elegir';
        $this->income_description = '';
        $this->income_amount = '';
        $this->discharge_description = '';
        $this->discharge_amount = '';
        $this->from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        $this->cover_reference = Cover::firstWhere('description','capital de trabajo inicial');
        $this->cover_reference_detail = $this->cover_reference->details->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->bank_account_cover = null;
        $this->bank_account_cover_detail = null;
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = Company::join('bank_accounts as b_a','b_a.company_id','companies.id')
            ->join('banks as b','b.id','b_a.bank_id')
            ->select('b_a.*','b.description as bank','companies.description as company')
            ->where('companies.description', 'like', '%' . $this->search . '%')
            ->orWhere('b.description', 'like', '%' . $this->search . '%')
            ->orWhere('b_a.type', 'like', '%' . $this->search . '%')
            ->orWhere('b_a.currency', 'like', '%' . $this->search . '%')
            ->orderBy('b_a.id','asc')
            ->paginate($this->pagination);

        else

            $data = Company::join('bank_accounts as b_a','b_a.company_id','companies.id')
            ->join('banks as b','b.id','b_a.bank_id')
            ->select('b_a.*','b.description as bank','companies.description as company')
            ->orderBy('b_a.id','asc')
            ->paginate($this->pagination);
        
        
        return view('livewire.bank_account.bank-accounts', [
            'accounts' => $data,
            'banks' => Bank::orderBy('id','asc')->get(),
            'companies' => Company::orderBy('id','asc')->get(),
            //'banks' => Bank::with('account_type')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store()
    {
        if (!$this->cover_reference_detail) {

            $this->emit('error-message','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [
                
                'company_id' => 'not_in:Elegir',
                'bank_id' => 'not_in:Elegir',
                'type' => 'not_in:Elegir',
                'currency' => 'not_in:Elegir',
                'amount' => 'required|numeric|size:0',

            ];

            $messages = [

                'company_id.not_in' => 'Seleccione una opcion',
                'bank_id.not_in' => 'Seleccione una opcion',
                'type.not_in' => 'Seleccione una opcion',
                'currency.not_in' => 'Seleccione una opcion',
                'amount.required' => 'Campo requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'amount.size' => 'El saldo debe ser igual a 0',

            ];

            $this->validate($rules, $messages);

            $bank_name = Bank::find($this->bank_id)->description;
            $company_name = Company::find($this->company_id)->description;
            $exist = Cover::firstWhere('description',$bank_name . ' ' . $this->type . ' ' . $this->currency . ' ' . $company_name);

            if ($exist) {

                $this->emit('error-message', 'Ya existe una cuenta bancaria identica.');
                return;

            } else {

                DB::beginTransaction();
            
                try {
                
                    $new_bank_account = BankAccount::create([
    
                        'type' => $this->type,
                        'currency' => $this->currency,
                        'amount' => $this->amount,
                        'bank_id' => $this->bank_id,
                        'company_id' => $this->company_id

                    ]);
    
                    $new_bank_account_cover = Cover::create([
                
                        'description' => $bank_name . ' ' . $new_bank_account->type . ' ' . $new_bank_account->currency . ' ' . $company_name,
                        'type' => 'depositos',
                        'balance' => $new_bank_account->amount

                    ]);

                    $new_bank_account_cover->details()->create([

                        'type' => $new_bank_account_cover->type,
                        'previus_day_balance' => $new_bank_account_cover->balance,
                        'ingress' => 0,
                        'egress' => 0,
                        'actual_balance' => $new_bank_account_cover->balance

                    ]);
    
                    DB::commit();
                    $this->emit('item-added', 'Registro Exitoso.');
                    $this->resetUI();
    
                } catch (Exception $e) {
                    
                    DB::rollback();
                    //$this->emit('error-message', $e->getMessage());
                    $this->emit('error-message', 'Algo salio mal.');
    
                }
            }
        }
    }

    public function Edit(BankAccount $account)
    {
        $this->selected_id = $account->id;
        $this->company_id = $account->company_id;
        $this->bank_id = $account->bank_id;
        $this->type = $account->type;
        $this->currency = $account->currency;
        $this->amount = floatval($account->amount);
        $this->action = 'Elegir';
        $this->income_description = '';
        $this->income_amount = '';
        $this->discharge_description = '';
        $this->discharge_amount = '';
        $this->emit('show-modal2', 'Abrir Modal');
    }

    public function updatedaction()
    {
        $this->income_description = '';
        $this->income_amount = '';
        $this->discharge_description = '';
        $this->discharge_amount = '';
    }

    public function Update()
    {
        if (!$this->cover_reference_detail) {

            $this->emit('error-message','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [
                
                'company_id' => 'not_in:Elegir',
                'bank_id' => 'not_in:Elegir',
                'type' => 'not_in:Elegir',
                'currency' => 'not_in:Elegir',
                'amount' => 'required|numeric',
                'action' => 'not_in:Elegir',
                'income_description' => 'exclude_unless:action,ingreso|required|min:10|max:255',
                'income_amount' => 'exclude_unless:action,ingreso|required|numeric|gt:0',
                'discharge_description' => 'exclude_unless:action,egreso|required|min:10|max:255',
                'discharge_amount' => 'exclude_unless:action,egreso|required|numeric|gt:0|lte:amount',

            ];

            $messages = [

                'company_id.not_in' => 'Seleccione una opcion',
                'bank_id.not_in' => 'Seleccione una opcion',
                'type.not_in' => 'Seleccione una opcion',
                'currency.not_in' => 'Seleccione una opcion',
                'amount.required' => 'Campo requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'action.not_in' => 'Seleccione una opcion',
                'income_description.required' => 'Campo requerido',
                'income_description.min' => 'Minimo 10 caracteres',
                'income_description.max' => 'Maximo 255 caracteres',
                'income_amount.required' => 'Campo requerido',
                'income_amount.numeric' => 'Este campo solo admite numeros',
                'income_amount.gt' => 'El monto debe ser mayor a 0',
                'discharge_description.required' => 'Campo requerido',
                'discharge_description.min' => 'Minimo 10 caracteres',
                'discharge_description.max' => 'Maximo 255 caracteres',
                'discharge_amount.required' => 'Campo requerido',
                'discharge_amount.numeric' => 'Este campo solo admite numeros',
                'discharge_amount.gt' => 'El monto debe ser mayor a 0',
                'discharge_amount.lte' => 'El monto debe ser menor o igual al saldo actual',

            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
            try {

                $account = BankAccount::find($this->selected_id);
                $bank_name = Bank::firstWhere('id',$account->bank_id)->description;
                $company_name = Company::firstWhere('id',$account->company_id)->description;
                $this->bank_account_cover = Cover::firstWhere('description',$bank_name . ' ' . $account->type . ' ' . $account->currency . ' ' . $company_name);
                $this->bank_account_cover_detail = $this->bank_account_cover->details->whereBetween('created_at',[$this->from, $this->to])->first();

                switch ($this->action) {

                    /*case 'Elegir': $account->update([

                        'type' => $this->type,
                        'currency' => $this->currency,
                        'amount' => $this->amount,
                        'company_id' => $this->company_id,
                        'bank_id' => $this->bank_id

                    ]);
                    
                    break;*/

                    case 'ingreso': 
                        
                        $detail = $account->details()->create([

                            'description' => $this->income_description,
                            'amount' => $this->income_amount,
                            'previus_balance' => $account->amount,
                            'actual_balance' => $account->amount + $this->income_amount

                        ]);
                    
                        if (!$detail) {

                            $this->emit('error-message', 'Error al registrar el detalle del movimiento.');
                            return;

                        } else {

                            $account->update([

                                'amount' => $account->amount + $detail->amount
                
                            ]);
                
                            $this->bank_account_cover->update([
                                
                                'balance' => $this->bank_account_cover->balance + $detail->amount
                    
                            ]);
                    
                            $this->bank_account_cover_detail->update([
                
                                'ingress' => $this->bank_account_cover_detail->ingress + $detail->amount,
                                'actual_balance' => $this->bank_account_cover_detail->actual_balance + $detail->amount
                
                            ]);

                        }

                    break;

                    case 'egreso': 
                        
                        $detail = $account->details()->create([

                            'description' => $this->discharge_description,
                            'amount' => $this->discharge_amount,
                            'previus_balance' => $account->amount,
                            'actual_balance' => $account->amount - $this->discharge_amount

                        ]);
                    
                        if (!$detail) {

                            $this->emit('error-message', 'Error al registrar el detalle del movimiento.');
                            return;

                        } else {

                            $account->update([

                                'amount' => $account->amount - $detail->amount
                
                            ]);
                
                            $this->bank_account_cover->update([
                                
                                'balance' => $this->bank_account_cover->balance - $detail->amount
                    
                            ]);
                    
                            $this->bank_account_cover_detail->update([
                
                                'egress' => $this->bank_account_cover_detail->egress + $detail->amount,
                                'actual_balance' => $this->bank_account_cover_detail->actual_balance - $detail->amount
                
                            ]);

                        }

                    break;
                    
                }

                DB::commit();
                $this->emit('item-updated', 'Registro actualizado.');
                $this->resetUI();

            } catch (Exception $e) {
                
                DB::rollback();
                //$this->emit('error-message', $e->getMessage());
                $this->emit('error-message', 'Algo salio mal.');

            }
        }
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(BankAccount $bank_account)
    {
        $bank_name = Bank::find($bank_account->bank_id)->description;
        $company_name = Company::find($bank_account->company_id)->description;
        $this->bank_account_cover = Cover::firstWhere('description',$bank_name . ' ' . $bank_account->type . ' ' . $bank_account->currency . ' ' . $company_name);

        if (!$this->bank_account_cover) {

            $this->emit('error-message','No se ha encontrado caratula para esta cuenta.');
            return;

        } else {

            $this->bank_account_cover_detail = $this->bank_account_cover->details->whereBetween('created_at',[$this->from, $this->to])->first();

            if (!$this->bank_account_cover_detail) {

                $this->emit('error-message','No se ha encontrado caratula del dia para esta cuenta.');
                return;

            } else {

                if ($bank_account->amount != 0 || $this->bank_account_cover->balance != 0 || $this->bank_account_cover_detail->actual_balance != 0) {

                    $this->emit('error-message', 'El saldo de la cuenta y su caratula relacionada deben ser exactamente 0 para poder eliminarla.');
                    return;
    
                } else {
    
                    DB::beginTransaction();
            
                    try {
                        
                        $this->bank_account_cover->delete();
                        $bank_account->delete();
                        DB::commit();
                        $this->emit('item-deleted', 'Registro eliminado.');
                        $this->resetUI();
    
                    } catch (Exception $e) {
                        
                        DB::rollback();
                        //$this->emit('error-message', $e->getMessage());
                        $this->emit('error-message', 'Algo salio mal.');
    
                    }
                }
            }
        }
    }

    public function resetUI()
    {
        $this->resetValidation();
        $this->mount();
        $this->render();
    }
}
