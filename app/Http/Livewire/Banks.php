<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Bank;
use Livewire\WithPagination;

class Banks extends Component
{
    use WithPagination;

    public $description,$search,$selected_id,$pageTitle,$componentName;
    private $pagination = 20;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'bancos';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   

        if(strlen($this->search) > 0)

            Bank::where('description', 'like', '%' . $this->search . '%')
            ->orderBy('description', 'asc')
            ->paginate($this->pagination);

        else

            $data = Bank::orderBy('banks.description', 'asc')
            ->paginate($this->pagination);

        return view('livewire.bank.banks', [

            'banks' => $data
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'description' => 'required|min:5|unique:banks'
        ];

        $messages = [

            'description.required' => 'El nombre del banco es requerido',
            'description.min' => 'El nombre del banco debe contener al menos 5 caracteres',
            'description.unique' => 'El banco ya fue registrado'
        ];

        $this->validate($rules, $messages);

        Bank::create([

            'description' => $this->description
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(Bank $bank){

        $this->selected_id = $bank->id;
        $this->description = $bank->description;

        $this->emit('show-modal', 'Mostrando modal');
    }

    public function Update(){
        
        $rules = [

            'description' => "required|min:5|unique:banks,description,{$this->selected_id}"
        ];

        $messages = [

            'description.required' => 'El nombre del banco es requerido',
            'description.min' => 'El nombre del banco debe contener al menos 5 caracteres',
            'description.unique' => 'El banco ya fue registrado'
        ];

        $this->validate($rules, $messages);

        $bank = Bank::find($this->selected_id);
        
        $bank->update([

            'description' => $this->description
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Bank $bank){
        
        $bank->delete();
        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){

        $this->description = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
