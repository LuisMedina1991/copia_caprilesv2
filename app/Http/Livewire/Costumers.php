<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Costumer;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CostumersImport;

class Costumers extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $description,$phone,$fax,$email,$nit,$search,$selected_id,$pageTitle,$componentName;
    private $pagination = 20;
    public $data_to_import;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'clientes';
        $this->data_to_import = null;
    }

    public function render()
    {   
        if(strlen($this->search) > 0){

            $data = Costumer::where('description', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orWhere('fax', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('nit', 'like', '%' . $this->search . '%')
            ->paginate($this->pagination);

        }else{

            $data = Costumer::orderBy('id', 'asc')->paginate($this->pagination);
        }

        return view('livewire.costumer.costumers', [
            'costumers' => $data,
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store(){

        $rules = [

            'description' => 'required|min:5|max:45|unique:costumers'
        ];

        $messages = [

            'description.required' => 'El nombre del cliente es requerido',
            'description.min' => 'El nombre del cliente debe contener al menos 5 caracteres',
            'description.max' => 'El nombre del cliente debe contener maximo 45 caracteres',
            'description.unique' => 'El nombre del cliente ya fue registrado'
        ];
        
        $this->validate($rules, $messages);

        Costumer::create([

            'description' => $this->description,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'nit' => $this->nit
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');

    }

    public function Edit(Costumer $costumer){
        
        $this->selected_id = $costumer->id;
        $this->description = $costumer->description;
        $this->phone = $costumer->phone;
        $this->fax = $costumer->fax;
        $this->email = $costumer->email;
        $this->nit = $costumer->nit;
        $this->emit('show-modal', 'Abrir Modal');

    }

    public function Update(){
        
        $rules = [

            'description' => "required|min:5|max:45|unique:costumers,description,{$this->selected_id}"
        ];

        $messages = [

            'description.required' => 'El nombre del cliente es requerido',
            'description.min' => 'El nombre del cliente debe contener al menos 5 caracteres',
            'description.max' => 'El nombre del cliente debe contener maximo 45 caracteres',
            'description.unique' => 'El nombre del cliente ya fue registrado'
        ];

        $this->validate($rules, $messages);

        $costumer = Costumer::find($this->selected_id);
        
        $costumer->Update([

            'description' => $this->description,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'nit' => $this->nit
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Costumer $costumer){
        
        $costumer->delete();
        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
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

            Excel::import(new CostumersImport,$this->data_to_import);
            $this->emit('import-successfull','Carga de datos exitosa.');
            $this->resetUI();

        } catch (\Exception $e) {

            $this->emit('movement-error', 'Error al cargar datos.');
            return;

        }

    }

    public function resetUI(){

        $this->description = '';
        $this->nit = '';
        $this->phone = '';
        $this->fax = '';
        $this->email = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->data_to_import = null;
        $this->resetValidation();
        $this->resetPage();
    }
}
