<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Office;
use App\Models\Product;
use Livewire\WithPagination;

class Offices extends Component
{
    use WithPagination;

    public $name, $address, $phone, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'sucursales';
    }

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {   
        if(strlen($this->search) > 0)

            $data = Office::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('address', 'like', '%' . $this->search . '%')
            ->paginate($this->pagination);

        else

            $data = Office::orderBy('id', 'asc')->paginate($this->pagination);

        return view('livewire.office.offices', ['offices' => $data])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Store(){

        $rules = [

            'name' => 'required|unique:offices|min:5'
        ];

        $messages = [

            'name.required' => 'El nombre de la sucursal es requerido',
            'name.unique' => 'La sucursal ya existe',
            'name.min' => 'El nombre de la sucursal debe contener al menos 5 caracteres'
        ];

        $this->validate($rules, $messages);

        $products = Product::with('offices')->get();

        $office = Office::create([

            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone
        ]);

        if($office){

            foreach($products as $product){

                $office->products()->attach($product->id,[
                    'stock' => 0,
                    'alerts' => 1
                ]);
            }
        }

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');
    }

    public function Edit(Office $office){

        $this->selected_id = $office->id;
        $this->name = $office->name;
        $this->address = $office->address;
        $this->phone = $office->phone;
        $this->emit('show-modal', 'Mostrando modal');
    }

    public function Update(){

        $rules = [
          
          'name' => "required|min:5|unique:offices,name,{$this->selected_id}"
        ];

        $messages = [

            'name.required' => 'El nombre de la sucursal es requerido',
            'name.unique' => 'La sucursal ya existe',
            'name.min' => 'El nombre de la sucursal debe contener al menos 5 caracteres'
        ];

        $this->validate($rules, $messages);

        $office = Office::find($this->selected_id);
        $office->update([

            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Office $office){

        $products = Product::with('offices')->get();
        
        foreach($products as $product){

            $office->products()->detach($product->id);
        }
        
        $office->delete();
        
        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){
        
        $this->name = '';
        $this->address = '';
        $this->phone = '';
        $this->selected_id = 0;
        $this->search = '';
        $this->resetValidation();
        $this->resetPage();
    }
}
