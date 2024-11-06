<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Subcategory;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Types extends Component
{
    use WithPagination;

    public $search,$selected_id,$pageTitle,$componentName,$cat_id,$sub_id,$cat_id2,$sub_id2;
    private $pagination = 20;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'tipos';
        $this->cat_id = 'Elegir';
        $this->sub_id = 'Elegir';
        $this->cat_id2 = 'Elegir';
        $this->sub_id2 = 'Elegir';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = Category::join('category_subcategory as c_s','c_s.category_id','categories.id')
            ->join('subcategories as sub','sub.id','c_s.subcategory_id')
            ->select('c_s.*','categories.name as category','sub.name as subcategory')
            ->where('categories.name', 'like', '%' . $this->search . '%')
            ->orWhere('sub.name', 'like', '%' . $this->search . '%')
            ->paginate($this->pagination);

        else

            $data = Category::join('category_subcategory as c_s','c_s.category_id','categories.id')
            ->join('subcategories as sub','sub.id','c_s.subcategory_id')
            ->select('c_s.*','categories.name as category','sub.name as subcategory')
            ->orderBy('c_s.id', 'asc')
            ->paginate($this->pagination);

        return view('livewire.type.types',[

            'types' => $data,
            'categories' => Category::orderBy('name','asc')->get(),
            'subcategories' => Subcategory::orderBy('name','asc')->get()

            ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'cat_id' => ['required','not_in:Elegir',Rule::unique('category_subcategory','category_id')->where(function ($query) {
                return $query->where('subcategory_id',$this->sub_id);
            })],
            'sub_id' => 'required|not_in:Elegir'
        ];

        $messages = [

            'cat_id.required' => 'La categoria es requerida',
            'cat_id.not_in' => 'Elija una categoria',
            'cat_id.unique' => 'La categoria ya tiene esa subcategoria',
            'sub_id.required' => 'La subcategoria requerida',
            'sub_id.not_in' => 'Elija una subcategoria',
        ];

        $this->validate($rules, $messages);

        $data = Category::find($this->cat_id);

        $data->subcategories()->attach($this->sub_id);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit($id){
        
        $data = Category::join('category_subcategory as c_s','c_s.category_id','categories.id')
        ->join('subcategories as sub','sub.id','c_s.subcategory_id')
        ->select('c_s.*','categories.name as category','sub.name as subcategory')
        ->firstWhere('c_s.id',$id);
        
        $this->selected_id = $data->id;
        $this->cat_id = $data->category_id;
        $this->sub_id = $data->subcategory_id;
        $this->cat_id2 = $data->category_id;
        $this->sub_id2 = $data->subcategory_id;
        $this->emit('show-modal', 'Abrir Modal');
    }

    public function Update(){
        
        $rules = [

            'cat_id' => ['required','not_in:Elegir',Rule::unique('category_subcategory','category_id')
            ->ignore($this->selected_id)
            ->where(function ($query) {
                return $query->where('subcategory_id',$this->sub_id);
            })],
            'sub_id' => 'required|not_in:Elegir'
        ];

        $messages = [

            'cat_id.required' => 'El codigo de producto es requerido',
            'cat_id.not_in' => 'Elija el codigo de algun producto',
            'cat_id.unique' => 'La categoria ya tiene esa subcategoria',
            'sub_id.required' => 'La sucursal es requerida',
            'sub_id.not_in' => 'Elija una sucursal'
        ];

        $this->validate($rules, $messages);
        
        $data = Category::find($this->cat_id);
        $data2 = Category::find($this->cat_id2);
        $verify = $data->subcategories->firstWhere('pivot.subcategory_id',$this->sub_id);
        
        if($verify == null){

            $data->subcategories()->attach($this->sub_id);
            $data2->subcategories()->detach($this->sub_id2);

        }else{

            $data->subcategories()->updateExistingPivot($this->sub_id,[]);
        }

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy($cat,$sub){

        $data = Category::find($cat);
        $data->subcategories()->detach($sub);

        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){
        
        $this->cat_id = 'Elegir';
        $this->sub_id = 'Elegir';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
