<?php

namespace App\Http\Livewire;

use App\Imports\CostumerReceivablesImport;
use App\Models\Costumer;
use App\Models\CostumerReceivable;
use App\Models\Cover;
use App\Models\Detail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;


class CostumerReceivables extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search,$selected_id,$pageTitle,$componentName,$my_total,$details;
    public $costumer,$amount,$description,$action,$discharge_description,$discharge_amount;
    public $from,$to,$cov,$cov_det;
    private $pagination = 20;
    public $data_to_import;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'listado';
        $this->componentName = 'clientes por cobrar';
        $this->search = '';
        $this->selected_id = 0;
        $this->costumer = 'Elegir';
        $this->amount = '';
        $this->description = '';
        //$this->my_total = 0;
        $this->details = [];
        $this->from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description',$this->componentName);
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->data_to_import = null;
    }

    public function render()
    {   
        $this->my_total = 0;

        if(strlen($this->search) > 0){

            $data = Costumer::join('costumer_receivables as c_r','c_r.costumer_id','costumers.id')
            ->select('c_r.*','costumers.description as costumer')
            ->where('costumers.description', 'like', '%' . $this->search . '%')
            ->orWhere('c_r.description', 'like', '%' . $this->search . '%')
            ->orderBy('costumer','asc')
            ->paginate($this->pagination);

            $this->my_total = Costumer::join('costumer_receivables as c_r','c_r.costumer_id','costumers.id')
            ->where('costumers.description', 'like', '%' . $this->search . '%')
            ->sum('c_r.amount');

        }else{

            $data = Costumer::join('costumer_receivables as c_r','c_r.costumer_id','costumers.id')
            ->select('c_r.*','costumers.description as costumer')
            ->orderBy('costumer','asc')
            ->paginate($this->pagination);

            $vars = CostumerReceivable::all();

            foreach($vars as $var){

                $this->my_total += $var->amount;
            }
            //$this->my_total = $this->cov->balance;
        }

        return view('livewire.costumer_receivable.costumer-receivables', [
            'clients' => $data,
            'costumers' => Costumer::orderBy('description','asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store()
    {
        if($this->cov_det != null){

            $rules = [

                'costumer' => 'not_in:Elegir',
                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];

            $messages = [

                'costumer.not_in' => 'Seleccione una opcion',
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros'
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
                try {

                    $debt = CostumerReceivable::create([

                        'costumer_id' => $this->costumer,
                        'description' => $this->description,
                        'amount' => $this->amount
                    ]);

                    if($debt){

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

    public function Edit(CostumerReceivable $client)
    {
        $this->selected_id = $client->id;
        $this->costumer = $client->costumer_id;
        $this->amount = floatval($client->amount);
        $this->description = $client->description;
        $this->action = 'Elegir';
        $this->discharge_description = '';
        $this->discharge_amount = '';
        $this->emit('show-modal2', 'Abrir Modal');
    }

    public function updatedaction()
    {
        $this->discharge_description = '';
        $this->discharge_amount = '';
    }

    public function Update()
    {
        if (!$this->cov_det) {

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;

        } else {

            $rules = [

                'costumer' => 'not_in:Elegir',
                'amount' => 'required|numeric',
                'description' => 'required|min:10|max:255',
                'action' => 'not_in:Elegir',
                'discharge_amount' => 'exclude_unless:action,egreso|required|numeric|gt:0|lte:amount',
                'discharge_description' => 'exclude_unless:action,egreso|required|min:10|max:255',

            ];

            $messages = [

                'costumer.not_in' => 'Seleccione una opcion',
                'amount.required' => 'Campo requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'description.required' => 'Campo requerido',
                'description.min' => 'Minimo 10 caracteres',
                'description.max' => 'Maximo 255 caracteres',
                'action.not_in' => 'Seleccione una opcion',
                'discharge_amount.required' => 'Campo requerido',
                'discharge_amount.numeric' => 'Este campo solo admite numeros',
                'discharge_amount.gt' => 'El monto debe ser mayor a 0',
                'discharge_amount.lte' => 'El monto debe ser menor o igual al saldo',
                'discharge_description.required' => 'Campo requerido',
                'discharge_description.min' => 'Minimo 10 caracteres',
                'discharge_description.max' => 'Maximo 255 caracteres',

            ];

            $this->validate($rules, $messages);

            DB::beginTransaction();
            
            try {

                $debt = CostumerReceivable::find($this->selected_id);

                switch ($this->action) {

                    case 'edicion':

                        $debt->Update([

                            'costumer_id' => $this->costumer,
                            'description' => $this->description

                        ]);

                    break;

                    case 'egreso':

                        $detail = $debt->details()->create([

                            'description' => $this->discharge_description,
                            'amount' => $this->discharge_amount,
                            'previus_balance' => $debt->amount,
                            'actual_balance' => $debt->amount - $this->discharge_amount
                            
                        ]);
    
                        if (!$detail) {

                            $this->emit('movement-error', 'Error al registrar el detalle del movimiento.');
                            return;
    
                        } else {

                            $debt->Update([
    
                                'amount' => $debt->amount - $detail->amount

                            ]);
    
                            $this->cov->update([
                        
                                'balance' => $this->cov->balance - $detail->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                
                                'egress' => $this->cov_det->egress + $detail->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $detail->amount
                
                            ]);

                        }

                    break;

                }

                DB::commit();
                $this->resetUI();
                $this->mount();
                $this->render();
                $this->emit('item-updated', 'Registro Actualizado.');

            } catch (Exception $e) {
                
                DB::rollback();
                //$this->emit('movement-error', $e->getMessage());
                $this->emit('movement-error', 'Algo salio mal.');

            }
        }
    }

    protected $listeners = [
        
        'destroy' => 'Destroy',
        'cancel' => 'Cancel',
    ];

    public function Destroy(CostumerReceivable $client){

        if($this->cov_det != null){

            DB::beginTransaction();
            
                try {
            
                    $this->cov->update([
                        
                        'balance' => $this->cov->balance - $client->amount

                    ]);

                    $this->cov_det->update([

                        'ingress' => $this->cov_det->ingress - $client->amount,
                        'actual_balance' => $this->cov_det->actual_balance - $client->amount

                    ]);

                    $client->delete();
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

    public function Details(CostumerReceivable $costumer){

        $this->details = $costumer->details;
        $this->emit('show-detail', 'Mostrando modal');
    }

    public function Cancel(Detail $det){

        if($this->cov_det != null){

        $client = CostumerReceivable::firstWhere('id',$det->detailable_id);

            DB::beginTransaction();
            
                try {

                    if(($det->actual_balance + $det->amount) == (number_format($client->amount,2) + $det->amount)){
                        
                        $client->update([
                    
                            'amount' => $client->amount + $det->amount
            
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

            Excel::import(new CostumerReceivablesImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI(){

        $this->costumer = 'Elegir';
        $this->amount = '';
        $this->description = '';
        $this->action = 'Elegir';
        $this->discharge_description = '';
        $this->discharge_amount = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
