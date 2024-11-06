<?php

namespace App\Http\Livewire;

use App\Models\Gym;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Cover;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GymsImport;

class Gyms extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $description,$amount,$search,$selected_id,$pageTitle,$componentName,$my_total,$details;
    public $from,$to,$cov,$cov_det;
    private $pagination = 20;
    public $data_to_import;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'gimnasio';
        //$this->my_total = 0;
        $this->from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description',$this->componentName);
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->details = [];
        $this->data_to_import = null;
    }

    public function render()
    {   
        $this->my_total = 0;

        if(strlen($this->search) > 0){

            $data = Gym::where('description', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'asc')
            ->paginate($this->pagination);

            $this->my_total = Gym::where('description', 'like', '%' . $this->search . '%')
            ->sum('amount');

        }else{

            $data = Gym::orderBy('id', 'asc')
            ->paginate($this->pagination);

            $vars = Gym::all();

            foreach($vars as $var){
    
                $this->my_total += $var->amount;
            }
            //$this->my_total = $this->cov->balance;
        }

        return view('livewire.gym.gyms', [

            'gyms' => $data
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store(){

        if($this->cov_det != null){

            $rules = [

                'description' => 'required|min:10|max:255',
                'amount' => 'required|numeric'
            ];
    
            $messages = [
    
                'description.required' => 'La descripcion es requerida',
                'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                'amount.required' => 'El monto es requerido',
                'amount.numeric' => 'Este campo solo admite numeros',
            ];
            
            $this->validate($rules, $messages);

            DB::beginTransaction();
            
                try {
            
                    $gym = Gym::create([
            
                        'description' => $this->description,
                        'amount' => $this->amount
                    ]);
                
                    if($gym){

                        if($this->cov->balance > 0){

                            if($this->amount > 0){

                                $this->cov->update([
                            
                                    'balance' => $this->cov->balance + $this->amount
                        
                                ]);
                        
                                $this->cov_det->update([
                        
                                    'ingress' => $this->cov_det->ingress + $this->amount,
                                    'actual_balance' => $this->cov_det->actual_balance + $this->amount
                        
                                ]);
            
                            }else{
            
                                $this->cov->update([
                            
                                    'balance' => $this->cov->balance + $this->amount
                        
                                ]);
                        
                                $this->cov_det->update([
                        
                                    'egress' => $this->cov_det->egress - $this->amount,
                                    'actual_balance' => $this->cov_det->actual_balance + $this->amount
                        
                                ]);
                            }

                        }else{

                            if($this->amount > 0){

                                $this->cov->update([
                            
                                    'balance' => $this->cov->balance + $this->amount
                        
                                ]);
                        
                                $this->cov_det->update([
                        
                                    'ingress' => $this->cov_det->ingress + $this->amount,
                                    'actual_balance' => $this->cov_det->actual_balance + $this->amount
                        
                                ]);
            
                            }else{
            
                                $this->cov->update([
                            
                                    'balance' => $this->cov->balance + $this->amount
                        
                                ]);
                        
                                $this->cov_det->update([
                        
                                    'egress' => $this->cov_det->egress - $this->amount,
                                    'actual_balance' => $this->cov_det->actual_balance + $this->amount
                        
                                ]);
                            }
                        }
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

    /*public function Edit(Gym $gym){
        
        $this->selected_id = $gym->id;
        $this->client = $gym->client;
        $this->type = $gym->type;
        $this->quantity = $gym->quantity;
        $this->description = $gym->description;
        $this->amount = $gym->amount;
        
        $this->emit('show-modal', 'Abrir Modal');

    }

    public function Update(){
        
        $gym = Gym::find($this->selected_id);

        $rules = [

            'client' => 'required|min:5',
            'type' => 'required|not_in:Elegir',
            'quantity' => 'required|numeric',
            'description' => 'required|min:10',
            'amount' => 'required|numeric'
        ];

        $messages = [

            'client.required' => 'El nombre del cliente es requerido',
            'client.min' => 'El nombre del cliente debe contener al menos 5 caracteres',
            'quantity.required' => 'El tiempo de suscripcion es requerido',
            'quantity.numeric' => 'Este campo solo admite numeros',
            'description.required' => 'La descripcion es requerida',
            'description.min' => 'La descripcion debe contener al menos 10 caracteres',
            'amount.required' => 'El monto es requerido',
            'amount.numeric' => 'Este campo solo admite numeros',
            'type.required' => 'Seleccione una opcion',
            'type.not_in' => 'Seleccione una opcion'
        ];

        $this->validate($rules, $messages);

        $gym->Update([

            'client' => $this->client,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'amount' => $this->amount
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }*/

    protected $listeners = [
        
        'destroy' => 'Destroy'
    ];

    public function Destroy(Gym $gym){

        if($this->cov_det != null){
        
            if(Carbon::parse($gym->created_at)->format('d-m-Y') == Carbon::today()->format('d-m-Y')){
                
                DB::beginTransaction();
            
                try {

                    if($this->cov->balance > 0){

                        if($gym->amount > 0){
                        
                            $this->cov->update([
                        
                                'balance' => $this->cov->balance - $gym->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                    
                                'ingress' => $this->cov_det->ingress - $gym->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $gym->amount
                    
                            ]);
            
                        }else{
            
                            $this->cov->update([
                    
                                'balance' => $this->cov->balance - $gym->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                    
                                'egress' => $this->cov_det->egress + $gym->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $gym->amount
                    
                            ]);               
                        }

                    }else{

                        if($gym->amount > 0){
                        
                            $this->cov->update([
                        
                                'balance' => $this->cov->balance - $gym->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                    
                                'ingress' => $this->cov_det->ingress - $gym->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $gym->amount
                    
                            ]);
            
                        }else{
            
                            $this->cov->update([
                    
                                'balance' => $this->cov->balance - $gym->amount
                    
                            ]);
                    
                            $this->cov_det->update([
                    
                                'egress' => $this->cov_det->egress + $gym->amount,
                                'actual_balance' => $this->cov_det->actual_balance - $gym->amount
                    
                            ]);               
                        }
                    }
            
                    $gym->delete();
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

                $this->emit('cover-error','No se pueden eliminar registros de dias pasados');
                return;
            }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia');
            return;
        }
        
    }

    public function Details(Gym $gym){

        $this->details = $gym->details;
        $this->emit('show-detail', 'Mostrando modal');
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

            Excel::import(new GymsImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI(){

        $this->description = '';
        $this->amount = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->details = [];
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
