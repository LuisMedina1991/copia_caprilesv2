<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $name, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'categorias';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = Category::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);

        else

            $data = Category::orderBy('id', 'asc')->paginate($this->pagination);

        return view('livewire.category.categories', ['categories' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'name' => 'required|unique:categories|min:5'
        ];

        $messages = [

            'name.required' => 'El nombre de la categoria es requerido',
            'name.unique' => 'La categoria ya existe',
            'name.min' => 'La categoria debe contener al menos 5 caracteres'
        ];

        $this->validate($rules, $messages);

        Category::create([

            'name' => $this->name   
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(Category $category){

        $this->selected_id = $category->id;
        $this->name = $category->name;
        $this->emit('show-modal', 'Mostrando modal');
    }

    public function Update(){

        $rules = [
            
          'name' => "required|min:5|unique:categories,name,{$this->selected_id}"
        ];

        $messages = [

            'name.required' => 'El nombre de la categoria es requerido',
            'name.min' => 'La categoria debe contener al menos 5 caracteres',
            'name.unique' => 'La categoria ya existe'
        ];

        $this->validate($rules, $messages);

        $category = Category::find($this->selected_id);

        $category->update([

            'name' => $this->name
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Category $category){
        
        $category->delete();
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
