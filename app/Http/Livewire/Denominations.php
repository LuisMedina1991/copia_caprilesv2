<?php

namespace App\Http\Livewire;

use App\Models\Denomination;
use Livewire\Component;
use Livewire\WithPagination;

class Denominations extends Component
{
    use WithPagination;

    public $type,$value,$search,$selected_id,$pageTitle,$componentName;
    private $pagination = 10;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'denominaciones';
        $this->type = 'Elegir';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if(strlen($this->search) > 0)

            $data = Denomination::where('type', 'like', '%' . $this->search . '%')->paginate($this->pagination);

        else

            $data = Denomination::orderBy('id', 'desc')->paginate($this->pagination);

        return view('livewire.denomination.denominations', ['coins' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'type' => 'required|not_in:Elegir',
            'value' => 'required|unique:denominations|numeric'
        ];
    
        $messages = [
            'type.required' => 'El tipo es requerido',
            'type.not_in' => 'Debe asignar un tipo',
            'value.required' => 'El valor es requerido',
            'value.unique' => 'El valor ya existe',
            'value.numeric' => 'Este campo solo admite numeros'
        ];

        $this->validate($rules, $messages);

        Denomination::create([

            'type' => $this->type,
            'value' => $this->value
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(Denomination $denomination){

        $this->selected_id = $denomination->id;
        $this->type = $denomination->type;
        $this->value = $denomination->value;
        $this->emit('show-modal', 'Mostrar modal!');
    }

    public function Update(){

        $rules = [

          'type' => 'required|not_in:Elegir',
          'value' => "required|numeric|unique:denominations,value,{$this->selected_id}" 
        ];

        $messages = [

            'type.required' => 'El tipo es requerido',
            'type.not_in' => 'Debe asignar un tipo',
            'value.required' => 'El valor es requerido',
            'value.unique' => 'El valor ya existe',
            'value.numeric' => 'Este campo solo admite numeros'
        ];

        $this->validate($rules, $messages);

        $denomination = Denomination::find($this->selected_id);

        $denomination->update([

            'type' => $this->type,
            'value' => $this->value
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Denomination $denomination){

        $denomination->delete();
        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){

        $this->type = 'Elegir';
        $this->value = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
