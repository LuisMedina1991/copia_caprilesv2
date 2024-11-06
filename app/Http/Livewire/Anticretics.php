<?php

namespace App\Http\Livewire;

use App\Imports\AnticreticsImport;
use App\Models\Anticretic;
use App\Models\Cover;
use App\Models\Detail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Anticretics extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search,$selected_id,$pageTitle,$componentName,$details;
    public $reference,$amount,$description,$action,$income_description,$discharge_description,$income_amount,$discharge_amount;
    public $from,$to,$cov,$cov_det;
    public $my_total;
    private $pagination = 20;
    public $data_to_import;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'listado';
        $this->componentName = 'anticreticos';
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

            $data = Anticretic::where('description', 'like', '%' . $this->search . '%')
            ->orWhere('reference', 'like', '%' . $this->search . '%')
            ->paginate($this->pagination);

            $this->my_total = Anticretic::where('reference', 'like', '%' . $this->search . '%')
            ->sum('amount');

        }else{

            $data = Anticretic::orderBy('id', 'asc')->paginate($this->pagination);

            $vars = Anticretic::all();

            foreach($vars as $var){

                $this->my_total += $var->amount;
            }
            //$this->my_total = $this->cov->balance;
        }

        return view('livewire.anticretic.anticretics', [

            'anticretics' => $data
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store()
    {
        if($this->cov_det != null){

            $rules = [

                'reference' => 'required|min:5|max:45',
                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];

            $messages = [

                'reference.required' => 'La referencia es requerida',
                'reference.min' => 'La referencia debe contener al menos 5 caracteres',
                'reference.max' => 'La referencia debe contener 45 caracteres como maximo',
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros'
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
                try {

                    Anticretic::create([

                        'reference' => $this->reference,
                        'description' => $this->description,
                        'amount' => $this->amount
                    ]);

                    $this->cov->update([
                        
                        'balance' => $this->cov->balance + $this->amount

                    ]);

                    $this->cov_det->update([

                        'ingress' => $this->cov_det->ingress + $this->amount,
                        'actual_balance' => $this->cov_det->actual_balance + $this->amount

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

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }

    }

    public function Edit(Anticretic $ant)
    {
        $this->selected_id = $ant->id;
        $this->reference = $ant->reference;
        $this->amount = floatval($ant->amount);
        $this->description = $ant->description;
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
        $this->discharge_description = '';
        $this->income_amount = '';
        $this->discharge_amount = '';
    }

    public function Update()
    {
        if (!$this->cov_det) {

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;

        } else {

            $rules = [

                'reference' => 'required|min:5|max:45',
                'amount' => 'required|numeric',
                'description' => 'required|min:10|max:255',
                'action' => 'not_in:Elegir',
                'income_description' => 'exclude_unless:action,ingreso|required|min:10|max:255',
                'income_amount' => 'exclude_unless:action,ingreso|required|numeric|gt:0',
                'discharge_description' => 'exclude_unless:action,egreso|required|min:10|max:255',
                'discharge_amount' => 'exclude_unless:action,egreso|required|numeric|gt:0|lte:amount',

            ];

            $messages = [

                'reference.required' => 'Campo requerido',
                'reference.min' => 'Minimo 5 caracteres',
                'reference.max' => 'Maximo 45 caracteres',
                'amount.required' => 'Campo requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
                'description.required' => 'Campo requerido',
                'description.min' => 'Minimo 10 caracteres',
                'description.max' => 'Maximo 255 caracteres',
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
                'discharge_amount.lte' => 'El monto debe ser menor o igual al saldo',

            ];

            $this->validate($rules, $messages);

            DB::beginTransaction();
            
            try {

                $ant = Anticretic::find($this->selected_id);
        
                switch ($this->action) {
                    
                    case 'edicion':

                        $ant->Update([

                            'reference' => $this->reference,
                            'description' => $this->description

                        ]);

                    break;

                    case 'ingreso':

                        $detail = $ant->details()->create([

                            'description' => $this->income_description,
                            'amount' => $this->income_amount,
                            'previus_balance' => $ant->amount,
                            'actual_balance' => $ant->amount + $this->income_amount
                            
                        ]);
            
                        if (!$detail) {
            
                            $this->emit('movement-error', 'Error al registrar el detalle del movimiento.');
                            return;

                        } else {

                            $ant->Update([
            
                                'amount' => $ant->amount + $detail->amount

                            ]);

                            $this->cov->update([
                        
                                'balance' => $this->cov->balance + $detail->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                
                                'ingress' => $this->cov_det->ingress + $detail->amount,
                                'actual_balance' => $this->cov_det->actual_balance + $detail->amount
                
                            ]);

                        }

                    break;

                    case 'egreso':

                        $detail = $ant->details()->create([

                            'description' => $this->discharge_description,
                            'amount' => $this->discharge_amount,
                            'previus_balance' => $ant->amount,
                            'actual_balance' => $ant->amount - $this->discharge_amount
                            
                        ]);
            
                        if (!$detail) {
                            
                            $this->emit('movement-error', 'Error al registrar el detalle del movimiento.');
                            return;

                        } else {

                            $ant->Update([
            
                                'amount' => $ant->amount - $detail->amount

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

    public function Destroy(Anticretic $ant){

        if($this->cov_det != null){

            DB::beginTransaction();
            
                try {
            
                    $this->cov->update([
                        
                        'balance' => $this->cov->balance - $ant->amount

                    ]);

                    $this->cov_det->update([

                        'ingress' => $this->cov_det->ingress - $ant->amount,
                        'actual_balance' => $this->cov_det->actual_balance - $ant->amount

                    ]);

                    $ant->delete();
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

    public function Details(Anticretic $ant){

        $this->details = $ant->details;
        $this->emit('show-detail', 'Mostrando modal');
    }

    public function Cancel(Detail $det){

        if($this->cov_det != null){

            $ant = Anticretic::firstWhere('id',$det->detailable_id);

            DB::beginTransaction();
            
                try {

                    if($det->actual_balance > $det->previus_balance){

                        if(($det->actual_balance - $det->amount) == (number_format($ant->amount,2) - $det->amount)){

                            $ant->update([
                            
                                'amount' => $ant->amount - $det->amount

                            ]);

                            $this->cov->update([

                                'balance' => $this->cov->balance - $det->amount

                            ]);

                            $this->cov_det->update([

                                'ingress' => $this->cov_det->ingress - $det->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $det->amount

                            ]);

                            $det->delete();
                            $this->emit('cancel-detail', 'Registro Anulado');

                        }else{

                            $this->emit('report-error', 'El saldo no coincide. Anule los movimientos mas recientes.');
                            return;
                        }

                    }else{

                        if(($det->actual_balance + $det->amount) == (number_format($ant->amount,2) + $det->amount)){
                            
                            $ant->update([
                        
                                'amount' => $ant->amount + $det->amount
                
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

            Excel::import(new AnticreticsImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI(){

        $this->description = '';
        $this->income_description = '';
        $this->discharge_description = '';
        $this->reference = '';
        $this->amount = '';
        $this->income_amount = '';
        $this->discharge_amount = '';
        $this->action = 'Elegir';
        $this->search = '';
        $this->selected_id = 0;
        //$this->my_total = 0;
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
