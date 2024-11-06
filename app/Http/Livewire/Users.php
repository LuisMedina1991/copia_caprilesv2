<?php

namespace App\Http\Livewire;

use App\Models\Sale;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User;

class Users extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name,$phone,$email,$status,$profile,$image,$password,$selected_id,$fileLoaded,$pageTitle,$componentName,$search;
    private $pagination = 15;

    public function paginationView(){

        return 'vendor.livewire.bootstrap';
    }

    public function mount(){

        $this->pageTitle = 'listado';
        $this->componentName = 'usuarios';
        $this->status = 'Elegir';
        $this->profile = 'Elegir';
    }

    public function render()
    {
        //$users = User::with('roles')->get();
        //dd($users);

        if(strlen($this->search) > 0)

            $data = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('profile', 'like', '%' . $this->search . '%')
            ->orWhere('status', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->select('*')->orderBy('name', 'asc')->paginate($this->pagination);

        else

            $data = User::select('*')
            ->orderBy('name', 'asc')->paginate($this->pagination);

        return view('livewire.user.users', [
            'users' => $data,
            'roles' => Role::orderBy('name', 'asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    protected $listeners = [

        'destroy' => 'Destroy',
        'resetUI' => 'resetUI'
    ];

    public function Store(){

        $rules = [

            'name' => 'required|min:3',
            'profile' => 'required|not_in:Elegir',
            'email' => 'required|unique:users|email',
            'status' => 'required|not_in:Elegir',
            'password' => 'required|min:8'
        ];

        $messages = [

            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre de usuario debe contener al menos 3 caracteres',
            'profile.required' => 'El perfil es requerido',
            'profile.not_in' => 'Seleccione una opcion',
            'email.required' => 'El correo es requerido',
            'email.unique' => 'El email ya ha sido registrado por otro usuario',
            'email.email' => 'Este campo requiere un email',
            'status.required' => 'El estado es requerido',
            'status.not_in' => 'Seleccione una opcion',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe contener al menos 8 caracteres'
        ];

        $this->validate($rules, $messages);

        $user = User::create([

            'name' => $this->name,
            'profile' => $this->profile,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'password' => bcrypt($this->password)
        ]);

        $user->syncRoles($this->profile);

        /*if($this->image){

            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/users', $customFileName);
            $user->image = $customFileName;
            $user->save();
        }*/

        $this->resetUI();
        $this->emit('item-added', 'Usuario registrado');
    }

    public function edit(User $user){

        $this->selected_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->profile = $user->profile;
        $this->status = $user->status;
        $this->email = $user->email;
        $this->password = '';
        //$this->image = $user->null;
        $this->emit('show-modal', 'Mostrar Modal');
    }

    public function Update(){

        $rules = [
            
            'email' => "required|email|unique:users,email,{$this->selected_id}",
            'name' => 'required|min:3',
            'status' => 'required|not_in:Elegir',
            'profile' => 'required|not_in:Elegir',
            'password' => 'required|min:8'
        ];

        $messages = [

            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre de usuario debe contener al menos 3 caracteres',
            'email.required' => 'El correo es requerido',
            'email.email' => 'Ingrese un correo valido',
            'email.unique' => 'El email ya esta registrado en el sistema',
            'status.required' => 'El estado es requerido',
            'status.not_in' => 'Elija un estado',
            'profile.required' => 'El perfil/rol es requerido',
            'profile.not_in' => 'Elija un perfil/rol',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe contener al menos 8 caracteres'
        ];

        $this->validate($rules, $messages);

        $user = User::find($this->selected_id);
        
        if($this->password != null){

        $user->update([

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'profile' => $this->profile,
            'password' => bcrypt($this->password)
        ]);

        }else{

            $user->update([

                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'profile' => $this->profile,
                'password' => bcrypt($user->password)
            ]);
        }

        $user->syncRoles($this->profile);
        //$user->syncPermissions($this->profile);

        /*if($this->image){

            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/users', $customFileName);
            $imageTemp = $user->image;
            $user->image = $customFileName;
            $user->save();

            if($imageTemp != null){

                if(file_exists('storage/users/' . $imageTemp)){
                    
                    unlink('storage/users/' . $imageTemp);
                }
            }
        }*/

        $this->resetUI();
        $this->emit('item-updated', 'Usuario actualizado');
    }

    public function Destroy(User $user){

        //$imageTemp = $user->image;

        if($user){

            $sales = Sale::where('user_id', $user->id)->count();

            if($sales > 0){

                $this->emit('user-with-sales', 'No es posible eliminar al usuario mientras tenga ventas registradas');

            }else{

                $user->delete();

                /*if($imageTemp != null){

                    if(file_exists('storage/users/' . $imageTemp )){
                        
                        unlink('storage/users/' . $imageTemp);
                    }
                }*/

                $this->resetUI();
                $this->emit('item-deleted', 'Usuario eliminado');
            }
        }
    }

    public function resetUI(){

        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->image = '';
        $this->search = '';
        $this->status = 'Elegir';
        $this->profile = 'Elegir';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }
}
