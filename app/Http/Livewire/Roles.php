<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination;

    public $roleName, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 10;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';

    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'roles';

    }

    public function render()
    {
        if(strlen($this->search) > 0)

            $roles = Role::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);

        else

            $roles = Role::orderBy('name', 'asc')->paginate($this->pagination);

        return view('livewire.role.roles', [ 
              
            'roles' => $roles
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function CreateRole(){

        $rules = ['roleName' => 'required|min:2|unique:roles,name'];

        $messages = [

            'roleName.required' => 'El nombre del rol es requerido',
            'roleName.unique' => 'El rol ya existe',
            'roleName.min' => 'El nombre del rol debe contener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        Role::create(['name' => $this->roleName]);

        $this->emit('item-added', 'Se registro el rol');
        $this->resetUI();

    }

    public function Edit(Role $role){

        $this->selected_id = $role->id;
        $this->roleName = $role->name;
        $this->emit('show-modal', 'Mostrar modal');

    }

    public function UpdateRole(){


        $rules = ['roleName' => "required|min:2|unique:roles,name, {$this->selected_id}"];

        $messages = [

            'roleName.required' => 'El nombre del rol es requerido',
            'roleName.unique' => 'El rol ya existe',
            'roleName.min' => 'El nombre del rol debe contener al menos 2 caracteres'
        ];

        $this->validate($rules, $messages);

        $role = Role::find($this->selected_id);
        $role->name = $this->roleName;
        $role->save();

        $this->emit('item-updated', 'Rol actualizado');
        $this->resetUI();

    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy($id){

        $permissionsCount = Role::find($id)->permissions->count();

        if($permissionsCount > 0){

            $this->emit('role-error', 'No se puede eliminar debido a relacion');
            return;
        }

        Role::find($id)->delete();
        $this->emit('item-deleted', 'Se elimino el rol');

    }

    public function resetUI(){

        $this->roleName = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }

}
