<?php

namespace App\Http\Livewire;

use App\Models\Income;
use Livewire\Component;
use App\Models\User;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\Product;
use App\Models\Office;
use App\Models\State;
use Carbon\Carbon;
use App\Models\Cover;
use App\Models\ProviderPayable;
use App\Models\Paydesk;

class Reports extends Component
{   
    
    public $componentName,$data,$details,$sumDetails,$countDetails,$reportRange,$userId,$search;
    public $dateFrom,$dateTo,$saleId,$reportType,$income,$sale,$transfer,$state_ok,$state_no;
    public $desde,$hasta,$cov,$cov_det,$prov,$prov_det,$gen,$gen_det;

    public function mount(){

        $this->componentName = 'REPORTES DE INGRESOS | EGRESOS | TRASPASOS';
        $this->data = [];
        $this->details = [];
        $this->sumDetails = 0;
        $this->countDetails = 0;
        $this->reportRange = 0;
        $this->reportType = 0;
        $this->userId = 0;
        $this->saleId = 0;
        $this->income = [];
        $this->transfer = [];
        $this->sale = [];
        $this->state_ok = State::firstWhere('name','realizado');
        $this->state_no = State::firstWhere('name','anulado');
        $this->desde = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 00:00:00';
        $this->hasta = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description','inventario');
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->desde, $this->hasta])->first();
    }

    public function render()
    {
        $this->ReportsByDate();

        return view('livewire.report.reports', [
            'users' => User::orderBy('name', 'asc')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function ReportsByDate(){

        if($this->reportRange == 0){

            $from = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 00:00:00';
            $to = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 23:59:59';

        }else{

            $from = Carbon::parse($this->dateFrom)->format('Y-m-d') . ' 00:00:00';
            $to = Carbon::parse($this->dateTo)->format('Y-m-d') . ' 23:59:59';
        }

        if($this->reportRange == 1 && ($this->dateFrom == '' || $this->dateTo == '')){

            $this->emit('report-error', 'Seleccione fecha de inicio y fecha de fin');
            return;
        }

        if(strlen($this->search) > 0){

            if($this->userId == 0 && $this->reportType == 0){

                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->where('incomes.state_id',$this->state_ok->id)
                ->orderBy('incomes.pf','asc')
                ->get();
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->where('transfers.state_id',$this->state_ok->id)
                ->orderBy('transfers.pf','asc')
                ->get();
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->where('sales.state_id',$this->state_ok->id)
                ->orderBy('sales.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 0){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->where('incomes.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('incomes.pf','asc')
                ->get();
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->where('transfers.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('transfers.pf','asc')
                ->get();
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->where('sales.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('sales.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 1){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('incomes.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 1){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->where('incomes.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('incomes.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 2){
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('transfers.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 2){
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->where('transfers.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('transfers.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 3){
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('sales.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 3){
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->where('sales.user_id', $this->userId)
                ->where('p.code', 'like', '%' . $this->search . '%')
                ->orderBy('sales.pf','asc')
                ->get();
            }

        }else{

            if($this->userId == 0 && $this->reportType == 0){

                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->orderBy('incomes.pf','asc')
                ->get();
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->orderBy('transfers.pf','asc')
                ->get();
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->orderBy('sales.pf','asc')
                ->get();

                //dd($this->sale);
            }
    
            if($this->userId != 0 && $this->reportType == 0){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->where('incomes.user_id', $this->userId)
                ->orderBy('incomes.pf','asc')
                ->get();
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->where('transfers.user_id', $this->userId)
                ->orderBy('transfers.pf','asc')
                ->get();
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->where('sales.user_id', $this->userId)
                ->orderBy('sales.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 1){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->orderBy('incomes.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 1){
    
                $this->income = Income::join('users as u','u.id','incomes.user_id')
                ->join('states as st','st.id','incomes.state_id')
                ->join('products as p','p.id','incomes.product_id')
                ->select('p.*','u.name as user','incomes.*')
                ->whereBetween('incomes.created_at', [$from, $to])
                ->where('incomes.state_id',$this->state_ok->id)
                ->where('incomes.user_id', $this->userId)
                ->orderBy('incomes.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 2){
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->orderBy('transfers.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 2){
    
                $this->transfer = Transfer::join('users as u','u.id','transfers.user_id')
                ->join('states as st','st.id','transfers.state_id')
                ->join('products as p','p.id','transfers.product_id')
                ->select('p.*','u.name as user','transfers.*')
                ->whereBetween('transfers.created_at', [$from, $to])
                ->where('transfers.state_id',$this->state_ok->id)
                ->where('transfers.user_id', $this->userId)
                ->orderBy('transfers.pf','asc')
                ->get();
            }
    
            if($this->userId == 0 && $this->reportType == 3){
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->orderBy('sales.pf','asc')
                ->get();
            }
    
            if($this->userId != 0 && $this->reportType == 3){
    
                $this->sale = Sale::join('users as u','u.id','sales.user_id')
                ->join('states as st','st.id','sales.state_id')
                ->join('products as p','p.id','sales.product_id')
                ->select('p.*','u.name as user','sales.*')
                ->whereBetween('sales.created_at', [$from, $to])
                ->where('sales.state_id',$this->state_ok->id)
                ->where('sales.user_id', $this->userId)
                ->orderBy('sales.pf','asc')
                ->get();
            }
        }

    }

    protected $listeners = [
        'remove_income' => 'Remove_Income',
        'remove_transfer' => 'Remove_Transfer',
        'remove_sale' => 'Remove_Sale',
    ];

    public function Remove_Income(Income $income){

        $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->desde, $this->hasta])->where('type','Ventas')->get();

        if(count($paydesk) == 0){

            $data = $income->join('products as p','p.id','incomes.product_id')
            ->join('office_product as o_p','o_p.product_id','p.id')
            ->join('offices as o','o.id','o_p.office_id')
            ->select('incomes.quantity as quantity','incomes.type as type','p.id as product','o_p.office_id as office','o_p.stock as stock')
            ->where('incomes.id',$income->id)
            ->where('o.name',$income->office)
            ->first();

            $product = Product::find($data->product);

            if($data->type == 'compra' || $data->type == 'importacion'){

                $this->prov = Cover::firstWhere('description','proveedores por pagar');
                $this->prov_det = $this->prov->details->where('cover_id',$this->prov->id)->whereBetween('created_at',[$this->desde, $this->hasta])->first();

                $provider = ProviderPayable::find($income->relation);

                if(count($provider->details) < 1){

                    $this->prov->update([
                
                        'balance' => $this->prov->balance - $income->total
            
                    ]);
            
                    $this->prov_det->update([
            
                        'ingress' => $this->prov_det->ingress - $income->total,
                        'actual_balance' => $this->prov_det->actual_balance - $income->total
            
                    ]);
        
                    $provider->delete();

                }else{

                    $this->emit('report-error', 'La deuda inicial sufrio cambios. Anule esos movimientos primero');
                    return;
                }
            }
            
            $product->offices()->updateExistingPivot($data->office,[

                'stock' => $data->stock - $data->quantity
            ]);

            $this->cov->update([
                
                'balance' => $this->cov->balance - $income->total

            ]);

            $this->cov_det->update([

                'ingress' => $this->cov_det->ingress - $income->total,
                'actual_balance' => $this->cov_det->actual_balance - $income->total

            ]);

            $income->Update([
                'state_id' => $this->state_no->id
            ]);

            $this->emit('income-deleted', 'Ingreso Anulado');
            $this->render();

        }else{

            $this->emit('report-error', 'Anule las ventas del dia desde caja general primero');
            return;
        }

        
    }

    public function Remove_Transfer(Transfer $transfer){

        $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->desde, $this->hasta])->where('type','Ventas')->get();

        if(count($paydesk) == 0){

            $transfer->Update([
                'state_id' => $this->state_no->id
            ]);
    
            $product = Product::find($transfer->product_id);
    
            $from = Office::firstWhere('name',$transfer->from_office)->id;
    
            $to = Office::firstWhere('name',$transfer->to_office)->id;
    
            $from_stock = $product->offices()->firstWhere('office_id',$from)->pivot->stock;
    
            $to_stock = $product->offices()->firstWhere('office_id',$to)->pivot->stock;
            
            $product->offices()->updateExistingPivot($from,[
                'stock' => $from_stock + $transfer->quantity
            ]);
    
            $product->offices()->updateExistingPivot($to,[
                'stock' => $to_stock - $transfer->quantity
            ]);
    
            $this->emit('transfer-deleted', 'Traspaso Anulado');
            $this->render();

        }else{

            $this->emit('report-error', 'Anule las ventas del dia desde caja general primero');
            return;
        }  
        
    }

    public function Remove_Sale(Sale $sale){

        $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->desde, $this->hasta])->where('type','Ventas')->get();

        if(count($paydesk) == 0){

            $sale->Update([
                'state_id' => $this->state_no->id
            ]);
            
            $data = $sale->join('products as p','p.id','sales.product_id')
            ->join('office_product as o_p','o_p.product_id','p.id')
            ->join('offices as o','o.id','o_p.office_id')
            ->select('sales.quantity as quantity','p.id as product','o_p.office_id as office','o_p.stock as stock')
            ->where('sales.id',$sale->id)
            ->where('o.name',$sale->office)
            ->first();
            
            $product = Product::find($data->product);
            
            $product->offices()->updateExistingPivot($data->office,[
                'stock' => $data->stock + $data->quantity
            ]);
    
            /*$this->cov->update([
                
                'balance' => $this->cov->balance + ($sale->product->cost * $sale->quantity)
    
            ]);
    
            $this->cov_det->update([
    
                'ingress' => $this->cov_det->ingress + ($sale->product->cost * $sale->quantity),
                'actual_balance' => $this->cov_det->actual_balance + ($sale->product->cost * $sale->quantity)
    
            ]);*/
    
            $this->emit('sale-deleted', 'Venta Anulada');
            $this->render();

        }else{

            $this->emit('report-error', 'Anule las ventas del dia desde caja general primero');
            return;
        }  
        
    }

}
