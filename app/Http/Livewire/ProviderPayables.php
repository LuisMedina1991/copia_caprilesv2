<?php

namespace App\Http\Livewire;

use App\Imports\ProviderPayablesImport;
use App\Models\Cover;
use App\Models\Detail;
use App\Models\ProviderPayable;
use App\Models\Provider;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ProviderPayables extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search,$selected_id,$pageTitle,$componentName,$my_total,$details;
    public $provider_id,$amount,$description,$action,$discharge_amount,$discharge_description;
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
        $this->componentName = 'proveedores por pagar';
        $this->search = '';
        $this->selected_id = 0;
        $this->provider_id = 'Elegir';
        $this->amount = '';
        $this->description = '';
        $this->my_total = 0;
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

            $data = ProviderPayable::join('providers as p','p.id','provider_payables.provider_id')
            ->select('provider_payables.*','p.description as provider')
            ->where('provider_payables.description', 'like', '%' . $this->search . '%')
            ->orWhere('p.description', 'like', '%' . $this->search . '%')
            ->orderBy('provider', 'asc')
            ->paginate($this->pagination);

            $this->my_total = ProviderPayable::join('providers as p','p.id','provider_payables.provider_id')
            ->where('p.description', 'like', '%' . $this->search . '%')
            ->sum('provider_payables.amount');

        }else{

            $data = ProviderPayable::join('providers as p','p.id','provider_payables.provider_id')
            ->select('provider_payables.*','p.description as provider')
            ->orderBy('provider', 'asc')
            ->paginate($this->pagination);

            $vars = ProviderPayable::all();

            foreach($vars as $var){
    
                $this->my_total += $var->amount;
            }
            //$this->my_total = $this->cov->balance;
        }

        return view('livewire.provider_payable.provider-payables', [

            'payables' => $data,
            'providers' => Provider::orderBy('description','asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store()
    {
        if($this->cov_det != null){

            $rules = [

                'provider_id' => 'not_in:Elegir',
                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];

            $messages = [

                'provider_id.not_in' => 'Seleccione una opcion',
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros'
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
                try {

                    $provider = ProviderPayable::create([

                        'description' => $this->description,
                        'amount' => $this->amount,
                        'provider_id' => $this->provider_id
                    ]);

                    if($provider){

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

    public function Edit(ProviderPayable $payable)
    {
        $this->selected_id = $payable->id;
        $this->provider_id = $payable->provider_id;
        $this->amount = floatval($payable->amount);
        $this->description = $payable->description;
        $this->action = 'Elegir';
        $this->discharge_amount = '';
        $this->discharge_description = '';
        $this->emit('show-modal2', 'Abrir Modal');
    }

    public function updatedaction()
    {
        $this->discharge_amount = '';
        $this->discharge_description = '';
    }

    public function Update()
    {
        if (!$this->cov_det) {

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [

                'provider_id' => 'not_in:Elegir',
                'amount' => 'required|numeric',
                'description' => 'required|min:10|max:255',
                'action' => 'not_in:Elegir',
                'discharge_amount' => 'exclude_unless:action,egreso|required|numeric|gt:0|lte:amount',
                'discharge_description' => 'exclude_unless:action,egreso|required|min:10|max:255',

            ];

            $messages = [

                'provider_id.not_in' => 'Seleccione una opcion',
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

                $debt = ProviderPayable::find($this->selected_id);
        
                switch ($this->action) {

                    case 'edicion':

                        $debt->Update([

                            'provider_id' => $this->provider_id,
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
                $this->emit('item-updated', 'Registro Actualizado.');
                $this->resetUI();
                $this->mount();
                $this->render();

            } catch (Exception $e) {
                
                DB::rollback();
                //$this->emit('error-message', $e->getMessage());
                $this->emit('movement-error', 'Algo salio mal.');

            }
        }
    }

    protected $listeners = [
        
        'destroy' => 'Destroy',
        'cancel' => 'Cancel',

    ];

    public function Destroy(ProviderPayable $payable)
    {
        if($this->cov_det != null){

            DB::beginTransaction();
            
                try {
        
                    $this->cov->update([
                        
                        'balance' => $this->cov->balance - $payable->amount

                    ]);

                    $this->cov_det->update([

                        'ingress' => $this->cov_det->ingress - $payable->amount,
                        'actual_balance' => $this->cov_det->actual_balance - $payable->amount

                    ]);

                    $payable->delete();
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

    public function Details(ProviderPayable $payable)
    {
        $this->details = $payable->details;
        $this->emit('show-detail', 'Mostrando modal');
    }

    public function Cancel(Detail $det)
    {
        if($this->cov_det != null){

            $provider = ProviderPayable::firstWhere('id',$det->detailable_id);

            DB::beginTransaction();
            
                try {

                    if(($det->actual_balance + $det->amount) == (number_format($provider->amount,2) + $det->amount)){
                        
                        $provider->update([
                    
                            'amount' => $provider->amount + $det->amount
            
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

    public function ImportData()
    {
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

            Excel::import(new ProviderPayablesImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI()
    {
        $this->provider_id = 'Elegir';
        $this->amount = '';
        $this->description = '';
        $this->action = 'Elegir';
        $this->discharge_amount = '';
        $this->discharge_description = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
