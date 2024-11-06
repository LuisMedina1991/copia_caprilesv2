<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;  //paquete facade para reporte pdf
use Carbon\Carbon;  //paquete para calendario personalizado
use App\Models\Sale;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Product;
use App\Models\Office;
use App\Models\BankAccount;
use App\Models\CoverDetail;
use App\Models\Paydesk;
use App\Models\Appropriation;
use App\Models\Anticretic;
use App\Models\Bill;
use App\Models\Costumer;
use App\Models\Gym;
use App\Models\Import;
use App\Models\OtherProvider;
use App\Models\OtherReceivable;
use App\Models\Payable;
use App\Models\ProviderPayable;

//use Maatwebsite\Excel\Facades\Excel;    //paquete facade para reporte excel

class ExportController extends Controller
{

    public function AppropiationReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $apps = Appropriation::where('reference', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $apps = Appropriation::where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }

        $pdf = PDF::loadView('pdf.appropiation_report', compact('my_total','search','apps'));

        return $pdf->stream('reporte.pdf');
    }

    public function AnticreticReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $ants = Anticretic::where('reference', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $ants = Anticretic::where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }

        $pdf = PDF::loadView('pdf.anticretic_report', compact('my_total','search','ants'));

        return $pdf->stream('reporte.pdf');
    }

    public function BillReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $bills = Bill::where('reference', 'like', '%' . $search . '%')
            ->where('amount','!=',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $bills = Bill::where('amount','!=',0)
            ->orderBy('reference','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.bill_report', compact('my_total','search','bills'));

        return $pdf->stream('reporte.pdf');
    }

    public function ImportReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $imports = Import::where('description', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('id','asc')
            ->get();

        }else{

            $imports = Import::where('amount','>',0)
            ->orderBy('id','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.import_report', compact('my_total','search','imports'));

        return $pdf->stream('reporte.pdf');
    }

    public function CheckReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $checks = Costumer::join('check_receivables as c_r','c_r.costumer_id','costumers.id')
            ->join('banks as b','b.id','c_r.bank_id')
            ->select('c_r.*','costumers.description as costumer','b.description as bank')
            ->where('costumers.description', 'like', '%' . $search . '%')
            ->where('c_r.amount','>',0)
            ->orderBy('costumers.description','asc')
            ->get();

        }else{

            $checks = Costumer::join('check_receivables as c_r','c_r.costumer_id','costumers.id')
            ->join('banks as b','b.id','c_r.bank_id')
            ->select('c_r.*','costumers.description as costumer','b.description as bank')
            ->where('c_r.amount','>',0)
            ->orderBy('costumers.description','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.check_report', compact('my_total','search','checks'));

        return $pdf->stream('reporte.pdf');
    }

    public function CostumerReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $clients = Costumer::join('costumer_receivables as c_r','c_r.costumer_id','costumers.id')
            ->select('c_r.*','costumers.description as costumer')
            ->where('costumers.description', 'like', '%' . $search . '%')
            ->where('c_r.amount','>',0)
            ->orderBy('costumers.description','asc')
            ->get();

        }else{

            $clients = Costumer::join('costumer_receivables as c_r','c_r.costumer_id','costumers.id')
            ->select('c_r.*','costumers.description as costumer')
            ->where('c_r.amount','>',0)
            ->orderBy('costumers.description','asc')
            ->get();

        }

        $pdf = PDF::loadView('pdf.costumer_report', compact('my_total','search','clients'));
        return $pdf->stream('reporte.pdf');

    }

    public function ProviderReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $provs = ProviderPayable::join('providers as p','p.id','provider_payables.provider_id')
            ->select('provider_payables.*','p.description as provider')
            ->where('p.description', 'like', '%' . $search . '%')
            ->where('provider_payables.amount','>',0)
            ->orderBy('p.description','asc')
            ->get();

        }else{

            $provs = ProviderPayable::join('providers as p','p.id','provider_payables.provider_id')
            ->select('provider_payables.*','p.description as provider')
            ->where('provider_payables.amount','>',0)
            ->orderBy('p.description','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.provider_report', compact('my_total','search','provs'));
        return $pdf->stream('reporte.pdf');
    }

    public function OtherProvReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $others = OtherProvider::where('reference', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $others = OtherProvider::where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.other_prov_report', compact('my_total','search','others'));

        return $pdf->stream('reporte.pdf');
    }

    public function PayableReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $payables = Payable::where('reference', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $payables = Payable::where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.payable_report', compact('my_total','search','payables'));

        return $pdf->stream('reporte.pdf');
    }

    public function OtherReceivableReport($my_total,$search = ''){

        if(strlen($search) > 0){

            $others = OtherReceivable::where('reference', 'like', '%' . $search . '%')
            ->where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();

        }else{

            $others = OtherReceivable::where('amount','>',0)
            ->orderBy('reference','asc')
            ->get();
        }

        $pdf = PDF::loadView('pdf.other_receivable_report', compact('my_total','search','others'));

        return $pdf->stream('reporte.pdf');
    }

    public function GymReport($my_total){

        $gyms = Gym::where('amount','!=',0)
        ->orderBy('id','asc')
        ->get();

        $pdf = PDF::loadView('pdf.gym_report', compact('my_total','gyms'));

        return $pdf->stream('reporte.pdf');
    }

    public function reportPDF($userId,$reportRange,$reportType,$dateFrom = null,$dateTo = null){  //metodo para reporte pdf

        $data = [];

        if($reportRange == 0){     //validar si el usuario esta seleccionando/deja por defecto el tipo de reporte (ventas del dia)

            $from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';    //dar formato personalizado a fecha de inicio y guardar en variable
            $to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';  //dar formato personalizado a fecha de fin y guardar en variable

        }else{

            $from = Carbon::parse($dateFrom)->format('Y-m-d') . ' 00:00:00';    //dar formato personalizado a fecha de inicio y guardar en variable
            $to = Carbon::parse($dateTo)->format('Y-m-d') . ' 23:59:59';    //dar formato personalizado a fecha de fin y guardar en variable

        }

        if($userId == 0 && $reportType == 0){     //validar si no se esta seleccionando un usuario (opcion todos)

            $income = Income::join('users as u','u.id','incomes.user_id')
            ->join('states as st','st.id','incomes.state_id')
            ->join('products as p','p.id','incomes.product_id')
            ->select('incomes.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('incomes.created_at', [$from, $to])
            ->where('incomes.state_id',8)
            ->get();

            $transfer = Transfer::join('users as u','u.id','transfers.user_id')
            ->join('states as st','st.id','transfers.state_id')
            ->join('products as p','p.id','transfers.product_id')
            ->select('transfers.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('transfers.created_at', [$from, $to])
            ->where('transfers.state_id',8)
            ->get();

            $sale = Sale::join('users as u','u.id','sales.user_id')
            ->join('states as st','st.id','sales.state_id')
            ->join('products as p','p.id','sales.product_id')
            ->select('sales.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.state_id',8)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('income','transfer','sale','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId != 0 && $reportType == 0){

            $income = Income::join('users as u','u.id','incomes.user_id')
            ->join('states as st','st.id','incomes.state_id')
            ->join('products as p','p.id','incomes.product_id')
            ->select('incomes.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('incomes.created_at', [$from, $to])
            ->where('incomes.state_id',8)
            ->where('incomes.user_id',$userId)
            ->get();

            $transfer = Transfer::join('users as u','u.id','transfers.user_id')
            ->join('states as st','st.id','transfers.state_id')
            ->join('products as p','p.id','transfers.product_id')
            ->select('transfers.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('transfers.created_at', [$from, $to])
            ->where('transfers.state_id',8)
            ->where('transfers.user_id',$userId)
            ->get();

            $sale = Sale::join('users as u','u.id','sales.user_id')
            ->join('states as st','st.id','sales.state_id')
            ->join('products as p','p.id','sales.product_id')
            ->select('sales.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.state_id',8)
            ->where('sales.user_id',$userId)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('income','transfer','sale','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId == 0 && $reportType == 1){

            $income = Income::join('users as u','u.id','incomes.user_id')
            ->join('states as st','st.id','incomes.state_id')
            ->join('products as p','p.id','incomes.product_id')
            ->select('incomes.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('incomes.created_at', [$from, $to])
            ->where('incomes.state_id',8)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('income','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId != 0 && $reportType == 1){

            $income = Income::join('users as u','u.id','incomes.user_id')
            ->join('states as st','st.id','incomes.state_id')
            ->join('products as p','p.id','incomes.product_id')
            ->select('incomes.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('incomes.created_at', [$from, $to])
            ->where('incomes.state_id',8)
            ->where('incomes.user_id',$userId)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('income','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId == 0 && $reportType == 2){

            $transfer = Transfer::join('users as u','u.id','transfers.user_id')
            ->join('states as st','st.id','transfers.state_id')
            ->join('products as p','p.id','transfers.product_id')
            ->select('transfers.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('transfers.created_at', [$from, $to])
            ->where('transfers.state_id',8)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('transfer','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId != 0 && $reportType == 2){

            $transfer = Transfer::join('users as u','u.id','transfers.user_id')
            ->join('states as st','st.id','transfers.state_id')
            ->join('products as p','p.id','transfers.product_id')
            ->select('transfers.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('transfers.created_at', [$from, $to])
            ->where('transfers.state_id',8)
            ->where('transfers.user_id',$userId)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('transfer','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId == 0 && $reportType == 3){

            $sale = Sale::join('users as u','u.id','sales.user_id')
            ->join('states as st','st.id','sales.state_id')
            ->join('products as p','p.id','sales.product_id')
            ->select('sales.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.state_id',8)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('sale','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

        if($userId != 0 && $reportType == 3){

            $sale = Sale::join('users as u','u.id','sales.user_id')
            ->join('states as st','st.id','sales.state_id')
            ->join('products as p','p.id','sales.product_id')
            ->select('sales.*','u.name as user','p.code as code','p.cost as cost','p.price as price','p.brand as brand')
            ->whereBetween('sales.created_at', [$from, $to])
            ->where('sales.state_id',8)
            ->where('sales.user_id',$userId)
            ->get();

            //validar si el usuario esta seleccionando/deja por defecto el usuario (todos) y guardar en variable
            $user = $userId == 0 ? 'Todos' : User::find($userId)->name;
            //metodo loadView de paquete DomPDF recibe entre parametros (vista, informacion a renderizar)
            $pdf = PDF::loadView('pdf.reporte', compact('sale','user','reportRange','reportType','dateFrom','dateTo'));   //cargar vista y guardar en variable

            //metodo stream de paquete DomPDF
            return $pdf->stream('reporte.pdf'); //visualizar pdf
            //metodo download de paquete DomPDF
            //return $pdf->download('reporte.pdf');   //descargar pdf
        }

    }

    public function reportStock($my_total){

        $stocks = Product::with('offices')
        ->orderBy('products.category_subcategory_id', 'asc')
        ->orderBy('products.ring', 'asc')
        ->orderBy('products.code', 'asc')
        ->get();

        $offices = Office::orderBy('id','asc')->get();

        $pdf = PDF::loadView('pdf.reporte_stock', compact('my_total','stocks','offices'));

        return $pdf->stream('reporte.pdf');
    }

    public function reportAccount($company_id,$reportRange,$dateFrom = null,$dateTo = null){


        if($reportRange == 0){     //validar si el usuario esta seleccionando/deja por defecto el tipo de reporte (ventas del dia)

            $from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';    //dar formato personalizado a fecha de inicio y guardar en variable
            $to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';  //dar formato personalizado a fecha de fin y guardar en variable

        }else{

            $from = Carbon::parse($dateFrom)->format('Y-m-d') . ' 00:00:00';    //dar formato personalizado a fecha de inicio y guardar en variable
            $to = Carbon::parse($dateTo)->format('Y-m-d') . ' 23:59:59';    //dar formato personalizado a fecha de fin y guardar en variable

        }
        
        if($company_id == 0){

            $details = BankAccount::join('details as d','d.detailable_id','bank_accounts.id')
            ->select('d.*')
            ->whereBetween('d.created_at', [$from, $to])
            ->where('d.detailable_type','App\Models\BankAccount')
            ->orderBy('d.detailable_id', 'asc')->get();

            $account = $company_id == 0 ? 'Todas las cuentas' : BankAccount::find($company_id);
            $pdf = PDF::loadView('pdf.account_report', compact('account','details','reportRange','dateFrom','dateTo'));

            return $pdf->stream('reporte.pdf');
            //return $pdf->download('reporte.pdf');

        }else{

            $details = BankAccount::join('details as d','d.detailable_id','bank_accounts.id')
            ->select('d.*')
            ->whereBetween('d.created_at', [$from, $to])
            ->where('d.detailable_id',$company_id)
            ->where('d.detailable_type','App\Models\BankAccount')
            ->orderBy('d.detailable_id', 'asc')->get();

            $account = $company_id == 0 ? 'Todas las cuentas' : BankAccount::find($company_id)->company->description;
            $pdf = PDF::loadView('pdf.account_report', compact('account','details','reportRange','dateFrom','dateTo'));

            return $pdf->stream('reporte.pdf');
            //return $pdf->download('reporte.pdf');
        }
    }

    public function CoverReport($reportRange,$date = null){

        if($reportRange == 0){

            $fecha1 = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 00:00:00';
            $fecha2 = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 23:59:59';

        }else{

            $fecha1 = Carbon::parse($date)->format('Y-m-d'). ' 00:00:00';
            $fecha2 = Carbon::parse($date)->format('Y-m-d'). ' 23:59:59';
        }

        $details = CoverDetail::whereBetween('created_at', [$fecha1, $fecha2])->orderBy('id', 'asc')->get();

        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        $sum4 = 0;
        $sum5 = 0;
        $sum6 = 0;
        $sum7 = 0;
        $sum8 = 0;
        $sum9 = 0;
        $sum10 = 0;

        foreach($details as $detail){

            if($detail->type == 'efectivo' || $detail->type == 'depositos'){

                $sum1 += $detail->actual_balance;
            }

            if($detail->type == 'mercaderia' || $detail->type == 'creditos'){

                $sum2 += $detail->actual_balance;
            }

            if($detail->type == 'por_pagar'){

                $sum4 += $detail->actual_balance;
            }

            if($detail->type == 'utilidad_diaria'){

                $sum6 += $detail->actual_balance;
            }

            if($detail->type == 'gasto_diario'){

                $sum7 += $detail->actual_balance;
            }

            if ($detail->cover) {

                if ($detail->cover->description == 'capital de trabajo inicial') {

                    $sum9 = $detail->actual_balance;
                    
                }
            }
        }

        $sum3 = $sum1 + $sum2;
        $sum5 = $sum3 - $sum4;
        $sum8 = $sum6 - $sum7;
        $sum10 = $sum5 - $sum9;

        $pdf = PDF::loadView('pdf.cover_report', compact('details','sum1','sum2','sum3','sum4','sum5','sum6','sum7','sum8','sum9','sum10','reportRange','date'));

        return $pdf->stream('reporte.pdf');
        //return $pdf->download('reporte.pdf');
    }

    public function PaydeskReport($reportRange,$reportType,$my_total,$dateFrom = null,$dateTo = null){

        if($reportRange == 0){

            $fecha1 = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 00:00:00';
            $fecha2 = Carbon::parse(Carbon::today())->format('Y-m-d') . ' 23:59:59';

        }else{

            $fecha1 = Carbon::parse($dateFrom)->format('Y-m-d'). ' 00:00:00';
            $fecha2 = Carbon::parse($dateTo)->format('Y-m-d'). ' 23:59:59';
        }

        if($reportType == 0){

            $details = Paydesk::orderBy('action', 'asc')->whereBetween('created_at', [$fecha1, $fecha2])->get();
            
        }else{
            
            $details = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$fecha1, $fecha2])->where('type',$reportType)->get();
        }

        //$details = Paydesk::orderBy('action', 'asc')->whereBetween('created_at', [$fecha1, $fecha2])->get();

        $pdf = PDF::loadView('pdf.paydesk_report', compact('details','reportRange','reportType','my_total','dateFrom','dateTo'));

        return $pdf->stream('reporte.pdf');
        //return $pdf->download('reporte.pdf');
    }

    /*public function reporteExcel($userId,$reportRange,$reportType,$dateFrom = null,$dateTo = null){

        $reportName = 'Reporte_' . uniqid() . '.xlsx';
        return Excel::download(new SalesExport($userId,$reportRange,$reportType,$dateFrom,$dateTo),$reportName );
    }*/
}
