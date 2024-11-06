<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Subcategory;
use Livewire\WithPagination;

class Subcategories extends Component
{
    use WithPagination;

    public $name, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 10;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'subcategorias';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = Subcategory::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);

        else

            $data = Subcategory::orderBy('id', 'asc')->paginate($this->pagination);

        return view('livewire.subcategory.subcategories', ['subcategories' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'name' => 'required|unique:subcategories|min:3'
        ];

        $messages = [

            'name.required' => 'El nombre de la subcategoria es requerido',
            'name.unique' => 'La subcategoria ya existe',
            'name.min' => 'La subcategoria debe contener al menos 3 caracteres'
        ];

        $this->validate($rules, $messages);

        Subcategory::create([

            'name' => $this->name   
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(Subcategory $subcategory){

        $this->selected_id = $subcategory->id;
        $this->name = $subcategory->name;
        $this->emit('show-modal', 'Mostrando modal');
    }

    public function Update(){

        $rules = [
           
          'name' => "required|min:3|unique:subcategories,name,{$this->selected_id}"
        ];

        $messages = [

            'name.required' => 'El nombre de la subcategoria es requerido',
            'name.min' => 'La subcategoria debe contener al menos 3 caracteres',
            'name.unique' => 'La subcategoria ya existe'
        ];

        $this->validate($rules, $messages);

        $subcategory = Subcategory::find($this->selected_id);

        $subcategory->update([

            'name' => $this->name
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Subcategory $subcategory){
        
        $subcategory->delete();
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
