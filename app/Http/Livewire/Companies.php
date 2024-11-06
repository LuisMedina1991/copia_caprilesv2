<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Company;
use Livewire\WithPagination;

class Companies extends Component
{
    use WithPagination;

    public $description,$nit,$type,$category,$address,$search,$selected_id,$pageTitle,$componentName;
    private $pagination = 10;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'empresas';
    }

    public function render()
    {   
        if(strlen($this->search) > 0){

            $data = Company::where('description', 'like', '%' . $this->search . '%')
            ->orWhere('nit', 'like', '%' . $this->search . '%')
            ->orWhere('address', 'like', '%' . $this->search . '%')
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->orWhere('category', 'like', '%' . $this->search . '%')
            ->orWhere('address', 'like', '%' . $this->search . '%')
            ->paginate($this->pagination);

        }else{

            $data = Company::orderBy('id', 'asc')->paginate($this->pagination);
        }

        return view('livewire.company.companies', [
            'companies' => $data,
        ])
        ->extends('layouts.theme.app')
        ->section('content');

    }

    public function Store(){

        $rules = [

            'description' => 'required|min:5|unique:companies',
            'nit' => 'required|min:5',
            'type' => 'required',
            'category' => 'required',
            'address' => 'required',
        ];

        $messages = [

            'description.required' => 'El nombre de la empresa es requerido',
            'description.min' => 'El nombre de la empresa debe contener al menos 5 caracteres',
            'description.unique' => 'El nombre de la empresa ya fue registrado',
            'nit.required' => 'El nit de la empresa es requerido',
            'nit.min' => 'El nit de la empresa debe contener al menos 5 caracteres',
            'nit.unique' => 'El nit de la empresa ya fue registrado',
            'type.required' => 'El tipo de empresa es requerido',
            'category.required' => 'La categoria de la empresa es requerida',
            'address.required' => 'La direccion de la empresa es requerida',
        ];
        
        $this->validate($rules, $messages);

        Company::create([

            'description' => $this->description,
            'nit' => $this->nit,
            'type' => $this->type,
            'category' => $this->category,
            'address' => $this->address
        ]);

        $this->resetUI();
        $this->emit('item-added', 'Registro Exitoso');

    }

    public function Edit(Company $company){
        
        $this->selected_id = $company->id;
        $this->description = $company->description;
        $this->nit = $company->nit;
        $this->type = $company->type;
        $this->category = $company->category;
        $this->address = $company->address;
        
        $this->emit('show-modal', 'Abrir Modal');

    }

    public function Update(){
        
        $rules = [

            'description' => "required|min:5|unique:companies,description,{$this->selected_id}",
            'nit' => 'required|min:5',
            'type' => 'required',
            'category' => 'required',
            'address' => 'required',
        ];

        $messages = [

            'description.required' => 'El nombre de la empresa es requerido',
            'description.min' => 'El nombre de la empresa debe contener al menos 5 caracteres',
            'description.unique' => 'El nombre de la empresa ya fue registrado',
            'nit.required' => 'El nit de la empresa es requerido',
            'nit.min' => 'El nit de la empresa debe contener al menos 5 caracteres',
            'nit.unique' => 'El nit de la empresa ya fue registrado',
            'type.required' => 'El tipo de empresa es requerido',
            'category.required' => 'La categoria de la empresa es requerida',
            'address.required' => 'La direccion de la empresa es requerida',
        ];

        $this->validate($rules, $messages);

        $company = Company::find($this->selected_id);
        
        $company->Update([

            'description' => $this->description,
            'nit' => $this->nit,
            'type' => $this->type,
            'category' => $this->category,
            'address' => $this->address
        ]);

        $this->resetUI();
        $this->emit('item-updated', 'Registro Actualizado');
    }

    protected $listeners = [
        'destroy' => 'Destroy'
    ];

    public function Destroy(Company $company){
        
        $company->delete();

        $this->resetUI();
        $this->emit('item-deleted', 'Registro Eliminado');
    }

    public function resetUI(){

        $this->description = '';
        $this->nit = '';
        $this->type = '';
        $this->category = '';
        $this->address = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
