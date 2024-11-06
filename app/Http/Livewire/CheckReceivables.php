<?php

namespace App\Http\Livewire;

use App\Models\Bank;
use App\Models\Costumer;
use Livewire\Component;
use App\Models\CheckReceivable;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Cover;
use App\Models\Detail;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CheckReceivablesImport;

class CheckReceivables extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $description,$costumer,$bank,$number,$amount,$search,$selected_id,$pageTitle,$componentName,$my_total,$details;
    public $from,$to,$cov,$cov_det;
    private $pagination = 20;
    public $data_to_import;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'cheques por cobrar';
        //$this->costumer = 'Elegir';
        //$this->bank = 'Elegir';
        //$this->my_total = 0;
        $this->details = [];
        $this->from = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description',$this->componentName);
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->data_to_import = null;
    }

    public function render()
    {   
        $this->my_total = 0;

        if(strlen($this->search) > 0){

            $data = Costumer::join('check_receivables as c_r','c_r.costumer_id','costumers.id')
            ->join('banks as b','b.id','c_r.bank_id')
            ->select('c_r.*','costumers.description as costumer','b.description as bank')
            ->where('c_r.description', 'like', '%' . $this->search . '%')
            ->orWhere('costumers.description', 'like', '%' . $this->search . '%')
            ->orWhere('b.description', 'like', '%' . $this->search . '%')
            ->orWhere('c_r.number', 'like', '%' . $this->search . '%')
            ->orderBy('costumers.description','asc')
            ->paginate($this->pagination);

            $this->my_total = Costumer::join('check_receivables as c_r','c_r.costumer_id','costumers.id')
            ->where('costumers.description', 'like', '%' . $this->search . '%')
            ->sum('c_r.amount');

        }else{

            $data = Costumer::join('check_receivables as c_r','c_r.costumer_id','costumers.id')
            ->join('banks as b','b.id','c_r.bank_id')
            ->select('c_r.*','costumers.description as costumer','b.description as bank')
            ->orderBy('costumers.description','asc')
            ->paginate($this->pagination);

            $vars = CheckReceivable::all();

            foreach($vars as $var){

                $this->my_total += $var->amount;
            }
            //$this->my_total = $this->cov->balance;
        }

        return view('livewire.check_receivable.check-receivables', [
            'checks' => $data,
            'costumers' => Costumer::orderBy('id','asc')->get(),
            'banks' => Bank::orderBy('id','asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store(){

        if($this->cov_det != null){

            $rules = [

                'costumer' => 'not_in:Elegir',
                'bank' => 'not_in:Elegir',
                'number' => 'required|numeric|max:45',
                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];

            $messages = [

                'costumer.not_in' => 'Seleccione una opcion',
                'bank.not_in' => 'Seleccione una opcion',
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'number.required' => 'El numero de cheque es requerido',
                'number.numeric' => 'Este campo solo admite numeros',
                'number.max' => 'El numero de cheque debe tener 45 digitos como maximo'
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
                try {

                    $check = CheckReceivable::create([

                        'costumer_id' => $this->costumer,
                        'bank_id' => $this->bank,
                        'description' => $this->description,
                        'amount' => $this->amount,
                        'number' => $this->number
                    ]);

                    if($check){

                        $this->cov->update([
                        
                            'balance' => $this->cov->balance + $this->amount
                
                        ]);
                
                        $this->cov_det->update([
                
                            'ingress' => $this->cov_det->ingress + $this->amount,
                            'actual_balance' => $this->cov_det->actual_balance + $this->amount
                
                        ]);
                    }

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

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }

    }

    public function Edit(CheckReceivable $check){
        
        $this->selected_id = $check->id;
        $this->description = $check->description;
        $this->costumer = $check->costumer_id;
        $this->bank = $check->bank_id;
        $this->number = $check->number;
        $this->amount = number_format($check->amount,2);
        
        $this->emit('show-modal', 'Abrir Modal');

    }

    public function Update(){

        if($this->cov_det != null){
        
            $rules = [

                'costumer' => 'not_in:Elegir',
                'bank' => 'not_in:Elegir',
                'number' => 'required|numeric|max:45',
                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];

            $messages = [

                'costumer.not_in' => 'Seleccione una opcion',
                'bank.not_in' => 'Seleccione una opcion',
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'number.required' => 'El numero de cheque es requerido',
                'number.numeric' => 'Este campo solo admite numeros',
                'number.max' => 'El numero de cheque debe tener 45 digitos como maximo'
            ];

            $this->validate($rules, $messages);

            $check = CheckReceivable::find($this->selected_id);

            $check->Update([

                'costumer_id' => $this->costumer,
                'bank_id' => $this->bank,
                'description' => $this->description,
                'number' => $this->number,
                'amount' => $this->amount
            ]);

            $this->resetUI();
            $this->mount();
            $this->render();
            $this->emit('item-updated', 'Registro Actualizado');

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }

    }

    protected $listeners = [

        'destroy' => 'Destroy',
        'cancel' => 'Cancel',
        'collect' => 'Collect',
    ];

    public function Collect(CheckReceivable $check){

        if($this->cov_det != null){

            DB::beginTransaction();
            
                try {

                    $detail = $check->details()->create([

                        'description' => 'Cheque dado de baja/cobrado en fecha' . ' ' . Carbon::parse(Carbon::today())->format('Y-m-d'),
                        'amount' => $check->amount,
                        'previus_balance' => $check->amount,
                        'actual_balance' => 0
                        
                    ]);

                    if($detail){

                        $this->cov->update([
                        
                            'balance' => $this->cov->balance - $check->amount
                
                        ]);
                
                        $this->cov_det->update([
                
                            'egress' => $this->cov_det->egress + $check->amount,
                            'actual_balance' => $this->cov_det->actual_balance - $check->amount
                
                        ]);

                        $check->update([
                        
                            'amount' => 0
                
                        ]);
                    }

                    DB::commit();
                    $this->emit('check-collected', 'Cheque Cobrado');
                    $this->resetUI();
                    $this->mount();
                    $this->render();

                } catch (Exception) {
                    
                    DB::rollback();
                    $this->emit('report-error', 'Algo salio mal');
                }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }

    }

    public function Destroy(CheckReceivable $check){

        if($this->cov_det != null){

            DB::beginTransaction();
            
                try {
            
                    $this->cov->update([
                        
                        'balance' => $this->cov->balance - $check->amount

                    ]);

                    $this->cov_det->update([

                        'ingress' => $this->cov_det->ingress - $check->amount,
                        'actual_balance' => $this->cov_det->actual_balance - $check->amount

                    ]);

                    $check->delete();
                    DB::commit();
                    $this->emit('item-deleted', 'Registro Eliminado');
                    $this->resetUI();
                    $this->mount();
                    $this->render();

                } catch (Exception) {
                    
                    DB::rollback();
                    $this->emit('movement-error', 'Algo salio mal');
                }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }

    }

    public function Details(CheckReceivable $check){

        $this->details = $check->details;
        $this->emit('show-detail', 'Mostrando modal');
    }

    public function Cancel(Detail $det){

        if($this->cov_det != null){

            $check = CheckReceivable::firstWhere('id',$det->detailable_id);

            DB::beginTransaction();
            
                try {

                    if(($det->actual_balance + $det->amount) == (number_format($check->amount,2) + $det->amount)){
                        
                        $check->update([
                    
                            'amount' => $check->amount + $det->amount
            
                        ]);
            
                        $this->cov->update([
                
                            'balance' => $this->cov->balance + $det->amount
            
                        ]);
            
                        $this->cov_det->update([
            
                            'egress' => $this->cov_det->egress - $det->amount,
                            'actual_balance' => $this->cov_det->actual_balance + $det->amount
            
                        ]);

                        $det->delete();
                        $this->emit('cancel-detail', 'Registro Anulado');

                    }else{

                        $this->emit('report-error', 'El saldo no coincide. Anule los movimientos mas recientes.');
                        return;
                    }

                    DB::commit();
                    $this->resetUI();
                    $this->mount();
                    $this->render();

                } catch (Exception) {
                    
                    DB::rollback();
                    $this->emit('report-error', 'Algo salio mal');
                }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }
        
    }

    public function ImportData(){

        $rules = [

            'data_to_import' => 'required|file|max:2048|mimes:csv,xls,xlsx'
        ];

        $messages = [

            'data_to_import.required' => 'Seleccione un archivo',
            'data_to_import.file' => 'Seleccione un archivo valido',
            'data_to_import.max' => 'Maximo 2 mb',
            'data_to_import.mimes' => 'Solo archivos excel'
        ];
        
        $this->validate($rules, $messages);

        try {

            Excel::import(new CheckReceivablesImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI(){

        $this->description = '';
        $this->number = '';
        $this->amount = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->costumer = 'Elegir';
        $this->bank = 'Elegir';
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
