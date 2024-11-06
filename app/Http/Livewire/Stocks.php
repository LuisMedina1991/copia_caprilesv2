<?php

namespace App\Http\Livewire;

use App\Models\Income;
use App\Models\State;
use Livewire\Component;
use App\Models\Office;
use App\Models\Product;
use App\Models\Transfer;
use Livewire\WithPagination;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Cover;
use App\Models\Paydesk;
use App\Models\ProviderPayable;
use App\Models\Provider;
use App\Models\Sale;

class Stocks extends Component
{
    use WithPagination;

    public $search,$pageTitle,$componentName,$selected_id,$formTitle,$my_total;
    public $from,$to,$cov,$cov_det,$prov_id,$description,$prov,$prov_det;
    public $office_id,$office_id_2,$product_id,$cant,$cant2,$type,$pf;
    private $pagination = 20;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'listado';
        $this->formTitle = 'Traspaso';
        $this->componentName = 'inventario';
        $this->my_total = 0;
        $this->from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description',$this->componentName);
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->prov = Cover::firstWhere('description','proveedores por pagar');
        $this->prov_det = $this->prov->details->where('cover_id',$this->prov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
        $this->pf = '';
    }

    public function render()
    {   
        //$this->my_total = $this->cov->balance;
        $vars = Product::with('offices')->get();
        
        foreach($vars as $var){

            foreach($var->offices as $office){

                $this->my_total += $var->cost * $office->pivot->stock;
            }
        }

        if(strlen($this->search) > 0){

            $data = Product::with('offices')
            ->where('products.code', 'like', '%' . $this->search . '%')
            ->orWhere('products.brand', 'like', '%' . $this->search . '%')
            ->orWhere('products.threshing', 'like', '%' . $this->search . '%')
            ->orWhere('products.tarp', 'like', '%' . $this->search . '%')
            ->orderBy('products.category_subcategory_id', 'asc')
            ->orderBy('products.ring', 'asc')
            ->orderBy('products.code', 'asc')
            ->paginate($this->pagination);

        }else{

            $data = Product::with('offices','state')
            ->orderBy('products.category_subcategory_id', 'asc')
            ->orderBy('products.ring', 'asc')
            ->orderBy('products.code', 'asc')
            ->paginate($this->pagination);
        }
        
        return view('livewire.stock.stocks', [
            'stocks' => $data,
            'offices' => Office::orderBy('id','asc')->get(),
            'offices2' => Office::orderBy('id','asc')->get(),
            'products' => Product::orderBy('id','asc')->get(),
            'states' => State::orderBy('id','asc')->get(),
            'providers' => Provider::orderBy('description','asc')->get(),
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function Edit($id)
    {
        $data = Product::join('office_product as stock','stock.product_id','products.id')
        ->join('offices','offices.id','stock.office_id')
        ->select('stock.*','products.code as product','offices.name as office')
        ->firstWhere('stock.id',$id);

        $this->selected_id = $data->id;
        $this->office_id = $data->office_id;
        $this->product_id = $data->product_id;
        $this->cant = $data->stock;
        $this->type = 'Elegir';
        $this->prov_id = 'Elegir';
        $this->emit('show-modal', 'Abrir Modal');
    }

    public function Update()
    {
        if($this->cov_det != null){

            $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->from, $this->to])->where('type', 'Ventas')->get();

            if (count($paydesk) == 0) {
        
                $rules = [

                    'product_id' => ['not_in:Elegir',Rule::unique('office_product','product_id')
                    ->ignore($this->selected_id)
                    ->where(function ($query) {
                        return $query->where('office_id',$this->office_id);
                    })],
                    'office_id' => 'not_in:Elegir',
                    'cant2' => 'required|numeric|integer',
                    'type' => 'not_in:Elegir',
                    'pf' => 'exclude_if:type,baja|required|numeric',
                    'description' => 'exclude_if:type,baja|exclude_if:type,devolucion|required|min:10|max:255',
                    'prov_id' => 'exclude_unless:type,compra|not_in:Elegir',

                ];

                $messages = [

                    'product_id.not_in' => 'Elija el codigo de algun producto',
                    'product_id.unique' => 'El producto ya existe en la sucursal',
                    'office_id.not_in' => 'Elija una sucursal',
                    'cant2.required' => 'La cantidad es requerida',
                    'cant2.numeric' => 'Este campo solo acepta numeros',
                    'cant2.integer' => 'Este campo solo acepta numeros enteros',
                    'type.not_in' => 'Seleccione una opcion',
                    'pf.required' => 'Ingrese N° de Recibo',
                    'pf.numeric' => 'Este campo solo acepta numeros',
                    'description.required' => 'La descripcion del ingreso es requerida',
                    'description.min' => 'La descripcion debe contener al menos 10 caracteres',
                    'description.max' => 'La descripcion debe contener 255 caracteres como maximo',
                    'prov_id.not_in' => 'Seleccione una opcion',

                ];
                
                $this->validate($rules, $messages);

                DB::beginTransaction();

                $product = Product::find($this->product_id);
                $office = Office::find($this->office_id);
                $state = State::firstWhere('name','realizado');
                
                    try {

                        switch($this->type){

                            case 'Elegir': 
                                
                                $product->offices()->updateExistingPivot($this->office_id,[
                
                                    'stock' => $this->cant2
                
                                ]);
                
                            break;

                            case 'baja': 

                                if($this->cant >= $this->cant2){

                                    $sale = Sale::create([

                                        'quantity' => $this->cant2,
                                        'total' => 0,
                                        'utility' => 0,
                                        'cash' => 0,
                                        'change' => 0,
                                        'office' => $office->name,
                                        'pf' => 0,
                                        'state_id' => $state->id,
                                        'user_id' => Auth()->user()->id,
                                        'product_id' => $product->id
                                    ]);
                    
                                    if($sale){

                                        $product->offices()->updateExistingPivot($this->office_id,[
                
                                            'stock' => $this->cant - $this->cant2
                        
                                        ]);
                                    }

                                }else{

                                    $this->emit('income-error', 'No puede dar de baja una cantidad mayor al stock actual');
                                    return;
                                }
                
                            break;
                
                            case 'devolucion': 

                                $income = Income::create([

                                    'quantity' => $this->cant2,
                                    'total' => $this->cant2 * $product->cost,
                                    'office' => $office->name,
                                    'pf' => $this->pf,
                                    'type' => $this->type,
                                    'relation' => 0,
                                    'state_id' => $state->id,
                                    'user_id' => Auth()->user()->id,
                                    'product_id' => $product->id,
                
                                ]);
                
                                if($income){
                
                                    $product->offices()->updateExistingPivot($this->office_id,[
                
                                        'stock' => $this->cant + $this->cant2
                    
                                    ]);
                
                                    $this->cov->update([
                                
                                        'balance' => $this->cov->balance + $income->total
                            
                                    ]);
                            
                                    $this->cov_det->update([
                        
                                        'ingress' => $this->cov_det->ingress + $income->total,
                                        'actual_balance' => $this->cov_det->actual_balance + $income->total
                        
                                    ]);
                
                                }
                
                            break;
                
                            case 'compra': 

                                $income = Income::create([

                                    'quantity' => $this->cant2,
                                    'total' => $this->cant2 * $product->cost,
                                    'office' => $office->name,
                                    'pf' => $this->pf,
                                    'type' => $this->type,
                                    'relation' => 0,
                                    'state_id' => $state->id,
                                    'user_id' => Auth()->user()->id,
                                    'product_id' => $product->id,
                
                                ]);
                
                                if($income){
                
                                    $product->offices()->updateExistingPivot($this->office_id,[
                
                                        'stock' => $this->cant + $this->cant2
                
                                    ]);
                
                                    $this->cov->update([
                                
                                        'balance' => $this->cov->balance + $income->total
                            
                                    ]);
                            
                                    $this->cov_det->update([
                        
                                        'ingress' => $this->cov_det->ingress + $income->total,
                                        'actual_balance' => $this->cov_det->actual_balance + $income->total
                        
                                    ]);
                
                                    $this->prov->update([
                                
                                        'balance' => $this->prov->balance + $income->total
                            
                                    ]);
                            
                                    $this->prov_det->update([
                        
                                        'ingress' => $this->prov_det->ingress + $income->total,
                                        'actual_balance' => $this->prov_det->actual_balance + $income->total
                        
                                    ]);
                
                                    $provider = ProviderPayable::create([
                
                                        'description' => $this->description,
                                        'amount' => $income->total,
                                        'provider_id' => $this->prov_id
                
                                    ]);

                                    $income->update([
                                
                                        'relation' => $provider->id
                            
                                    ]);
                                }
                
                            break;
                
                            case 'importacion':

                                $provider = Provider::firstWhere('description',$this->type);

                                $income = Income::create([

                                    'quantity' => $this->cant2,
                                    'total' => $this->cant2 * $product->cost,
                                    'office' => $office->name,
                                    'pf' => $this->pf,
                                    'type' => $this->type,
                                    'relation' => 0,
                                    'state_id' => $state->id,
                                    'user_id' => Auth()->user()->id,
                                    'product_id' => $product->id,
                
                                ]);
                
                                if($income){
                
                                    $product->offices()->updateExistingPivot($this->office_id,[
                
                                        'stock' => $this->cant + $this->cant2
                
                                    ]);
                
                                    $this->cov->update([
                                
                                        'balance' => $this->cov->balance + $income->total
                            
                                    ]);
                            
                                    $this->cov_det->update([
                        
                                        'ingress' => $this->cov_det->ingress + $income->total,
                                        'actual_balance' => $this->cov_det->actual_balance + $income->total
                        
                                    ]);
                
                                    $this->prov->update([
                                
                                        'balance' => $this->prov->balance + $income->total
                            
                                    ]);
                            
                                    $this->prov_det->update([
                        
                                        'ingress' => $this->prov_det->ingress + $income->total,
                                        'actual_balance' => $this->prov_det->actual_balance + $income->total
                        
                                    ]);
                
                                    $provider_payable = ProviderPayable::create([
                
                                        'description' => $this->description,
                                        'amount' => $income->total,
                                        'provider_id' => $provider->id
                
                                    ]);

                                    $income->update([
                                
                                        'relation' => $provider_payable->id
                            
                                    ]);
                                }
                
                            break;
                        }

                        DB::commit();
                        $this->emit('item-updated', 'Stock Actualizado.');
                        $this->resetUI();
                        $this->mount();
                        $this->render();

                    } catch (Exception) {
                        
                        DB::rollback();
                        $this->emit('income-error', 'Algo salio mal.');
                    }

            } else {

                $this->emit('cover-error', 'Anule las ventas del dia desde caja general primero');
                return;
            }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;
        }
    }

    public function Charge($id)
    {
        $data = Product::join('office_product as stock','stock.product_id','products.id')
        ->join('offices','offices.id','stock.office_id')
        ->select('stock.*','products.code as product','offices.name as office')
        ->firstWhere('stock.id',$id);
        
        $this->selected_id = $data->id;
        $this->office_id = $data->office_id;
        $this->office_id_2 = 'Elegir';
        $this->product_id = $data->product_id;
        $this->cant = $data->stock;
        $this->cant2 = $data->stock;
        $this->emit('show-modal2', 'Abrir Modal');
    }

    public function Transfer()
    {
        if($this->cov_det != null){

            $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->from, $this->to])->where('type','Ventas')->get();

            if(count($paydesk) == 0){

                $rules = [

                    'product_id' => 'required|not_in:Elegir',
                    'office_id' => 'required|not_in:Elegir',
                    'office_id_2' => "required|not_in:Elegir,$this->office_id",
                    'cant' => 'required',
                    'cant2' => "required|lte:$this->cant|numeric|integer",
                    'pf' => 'required|numeric',
                ];

                $messages = [

                    'product_id.required' => 'El codigo de producto es requerido',
                    'product_id.not_in' => 'Elija el codigo de algun producto',
                    'office_id.required' => 'La sucursal de origen es requerida',
                    'office_id.not_in' => 'Elija una sucursal de origen',
                    'office_id_2.required' => 'La sucursal de destino es requerida',
                    'office_id_2.not_in' => 'Elija una sucursal de destino diferente',
                    'cant.required' => 'El stock es requerido',
                    'cant2.required' => 'La cantidad a mover es requerida',
                    'cant2.lte' => 'La cantidad es mayor al stock',
                    'cant2.numeric' => 'Este campo solo acepta numeros',
                    'cant2.integer' => 'Este campo solo acepta numeros enteros',
                    'pf.required' => 'Ingrese N° de Recibo',
                    'pf.numeric' => 'Este campo solo acepta numeros',
                ];

                $this->validate($rules, $messages);

                DB::beginTransaction();
                
                try{

                    $product = Product::find($this->product_id);
                    $office_1 = Office::find($this->office_id);
                    $office_2 = Office::find($this->office_id_2);
                    $state = State::firstWhere('name','realizado');
                    
                    Transfer::create([

                        'quantity' => $this->cant2,
                        'total' => $product->cost * $this->cant2,
                        'pf' => $this->pf,
                        'from_office' => $office_1->name,
                        'to_office' => $office_2->name,
                        'state_id' => $state->id,
                        'user_id' => Auth()->user()->id,
                        'product_id' => $product->id,

                    ]);

                    $product->offices()->updateExistingPivot($this->office_id,[

                        'stock' => $this->cant - $this->cant2

                    ]);
                    
                    $product->offices()->updateExistingPivot($this->office_id_2,[

                        'stock' => $product->offices()->get()->firstWhere('pivot.office_id',$this->office_id_2)->pivot->stock + $this->cant2

                    ]);
                    
                    DB::commit();
                    $this->emit('item-transfered', 'Producto Traspasado.');
                    $this->resetUI();
                    $this->mount();
                    $this->render();

                }catch(Exception){

                    DB::rollback();
                    $this->emit('income-error','Algo salio mal.');
                }

            }else{

                $this->emit('cover-error', 'Anule las ventas del dia desde caja general primero');
                return;
            }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;
        }

    }

    protected $listeners = [

        'destroy' => 'Destroy'
    ];

    public function Destroy(Product $product)
    {
        if($this->cov_det != null){

            if(($product->offices->sum('pivot.stock')) < 1){

                DB::beginTransaction();
            
                try {

                    $offices = Office::with('products')->get();
                    
                    foreach($offices as $office){

                        $product->offices()->detach($office->id);
                    }

                    $product->delete();
                    DB::commit();
                    $this->emit('item-deleted', 'Producto eliminado.');
                    $this->resetUI();
                    $this->mount();
                    $this->render();

                } catch (Exception) {
                    
                    DB::rollback();
                    $this->emit('income-error', 'Algo salio mal.');
                }

            }else{

                $this->emit('income-error', 'No se puede eliminar.');
                return;
            }

        }else{

            $this->emit('cover-error','Se debe crear caratula del dia.');
            return;
        }
    }

    public function resetUI()
    {
        $this->office_id_2 = 'Elegir';
        $this->cant = '';
        $this->cant2 = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->my_total = 0;
        $this->type = 'Elegir';
        $this->prov_id = 'Elegir';
        $this->description = '';
        $this->pf = '';
        $this->resetValidation();
        $this->resetPage();
    }
}
