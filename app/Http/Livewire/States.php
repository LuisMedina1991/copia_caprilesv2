<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\State;
use Livewire\WithPagination;

class States extends Component
{
    use WithPagination;

    public $name, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 10;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'estados';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = State::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);

        else

            $data = State::orderBy('id', 'asc')->paginate($this->pagination);

        return view('livewire.state.states', ['states' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'name' => 'required|unique:states|min:5'
        ];

        $messages = [

            'name.required' => 'El nombre del status es requerido',
            'name.unique' => 'El status ya existe',
            'name.min' => 'El status debe contener al menos 5 caracteres'
        ];

        $this->validate($rules, $messages);

        State::create([

            'name' => $this->name   
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(State $state){

        $this->selected_id = $state->id;
        $this->name = $state->name;
        $this->emit('show-modal', 'Mostrando modal');
    }

    public function Update(){

        $rules = [
           
          'name' => "required|min:5|unique:states,name,{$this->selected_id}"
        ];

        $messages = [

            'name.required' => 'El nombre del status es requerido',
            'name.unique' => 'El status ya existe',
            'name.min' => 'El status debe contener al menos 5 caracteres'
        ];

        $this->validate($rules, $messages);

        $state = State::find($this->selected_id);

        $state->update([

            'name' => $this->name
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(State $state){
        
        $state->delete();
        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){

        $this->name = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
