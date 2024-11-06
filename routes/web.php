<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Categories;
use App\Http\Livewire\Denominations;
use App\Http\Livewire\Subcategories;
use App\Http\Livewire\Offices;
use App\Http\Livewire\Products;
use App\Http\Livewire\Reports;
use App\Http\Livewire\Sales;
use App\Http\Livewire\States;
use App\Http\Livewire\Stocks;
use App\Http\Livewire\Types;
use App\Http\Controllers\ExportController;
use App\Http\Livewire\Anticretics;
use App\Http\Livewire\Appropiations;
use App\Http\Livewire\Asignar;
use App\Http\Livewire\BankAccountReports;
use App\Http\Livewire\BankAccounts;
use App\Http\Livewire\Banks;
use App\Http\Livewire\Bills;
use App\Http\Livewire\CheckReceivables;
use App\Http\Livewire\Companies;
use App\Http\Livewire\CostumerReceivables;
use App\Http\Livewire\Costumers;
use App\Http\Livewire\Imports;
use App\Http\Livewire\OtherReceivables;
use App\Http\Livewire\Payables;
use App\Http\Livewire\Paydesks;
use App\Http\Livewire\ProviderPayables;
use App\Http\Livewire\Providers;
use App\Http\Livewire\CoverReports;
use App\Http\Livewire\Gyms;
use App\Http\Livewire\OtherProviders;
use App\Http\Livewire\Permisos;
use App\Http\Livewire\Roles;
use App\Http\Livewire\Users;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return view('auth.login');
});

//Auth::routes();
Auth::routes([
    'register' => false,
    'reset' => false,
]);


Route::middleware(['auth'])->group(function(){

    //Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('home', CoverReports::class)->name('home');
    Route::get('covers', CoverReports::class)->middleware('permission:vista_caratula');
    Route::get('paydesks', Paydesks::class)->middleware('permission:vista_caja_general');
    Route::get('offices', Offices::class)->middleware('permission:vista_sucursales');
    Route::get('products', Products::class)->middleware('permission:vista_productos');
    Route::get('stocks', Stocks::class)->middleware('permission:vista_stock');
    Route::get('sales', Sales::class)->middleware('permission:vista_ventas');
    Route::get('reports', Reports::class)->middleware('permission:vista_reportes_almacen');
    Route::get('bank_account_reports', BankAccountReports::class)->middleware('permission:vista_reportes_bancarios');
    Route::get('anticretics', Anticretics::class)->middleware('permission:vista_anticreticos');
    Route::get('appropiations', Appropiations::class)->middleware('permission:vista_consignaciones');
    Route::get('accounts', BankAccounts::class)->middleware('permission:vista_cuentas_bancos');
    Route::get('bills', Bills::class)->middleware('permission:vista_facturas');
    Route::get('checks', CheckReceivables::class)->middleware('permission:vista_cheques_por_cobrar');
    Route::get('costumers_debts', CostumerReceivables::class)->middleware('permission:vista_clientes_por_cobrar');
    Route::get('imports', Imports::class)->middleware('permission:vista_mercaderia_en_transito');
    Route::get('other_providers', OtherProviders::class)->middleware('permission:vista_otros_proveedores');
    Route::get('others', OtherReceivables::class)->middleware('permission:vista_otros_por_cobrar');
    Route::get('payables', Payables::class)->middleware('permission:vista_otros_por_pagar');
    Route::get('provider_payables', ProviderPayables::class)->middleware('permission:vista_proveedores_por_pagar');
    Route::get('gyms', Gyms::class)->middleware('permission:vista_gimnasio');
    Route::get('categories', Categories::class)->middleware('permission:vista_categorias');
    Route::get('subcategories', Subcategories::class)->middleware('permission:vista_subcategorias');
    Route::get('types', Types::class)->middleware('permission:vista_tipos');
    Route::get('denominations', Denominations::class)->middleware('permission:vista_monedas');
    Route::get('states', States::class)->middleware('permission:vista_estados');
    Route::get('banks', Banks::class)->middleware('permission:vista_bancos');
    Route::get('companies', Companies::class)->middleware('permission:vista_empresas');
    Route::get('costumers', Costumers::class)->middleware('permission:vista_clientes');
    Route::get('providers', Providers::class)->middleware('permission:vista_proveedores');
    

    Route::group(['middleware' => ['role:admin']],function(){

        Route::get('roles', Roles::class);
        Route::get('permisos', Permisos::class);
        Route::get('asignar', Asignar::class);
        Route::get('users', Users::class);
    });

    //REPORTES PDF
    Route::get('report/pdf/{user}/{range}/{type}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
    Route::get('report/pdf/{user}/{range}/{type}', [ExportController::class, 'reportPDF']);
    Route::get('report_stock/pdf/{total}', [ExportController::class, 'reportStock']);
    Route::get('bank_account_report/pdf/{company}/{range}/{f1}/{f2}', [ExportController::class, 'reportAccount']);
    Route::get('bank_account_report/pdf/{company}/{range}', [ExportController::class, 'reportAccount']);
    Route::get('cover_report/pdf/{range}/{f1}', [ExportController::class, 'CoverReport']);
    Route::get('cover_report/pdf/{range}', [ExportController::class, 'CoverReport']);
    Route::get('paydesk_report/pdf/{range}/{type}/{total}/{f1}/{f2}', [ExportController::class, 'PaydeskReport'])->where('type','.*');
    Route::get('paydesk_report/pdf/{range}/{type}/{total}', [ExportController::class, 'PaydeskReport']);
    Route::get('appropiation_report/pdf/{total}/{search}', [ExportController::class, 'AppropiationReport']);
    Route::get('appropiation_report/pdf/{total}', [ExportController::class, 'AppropiationReport']);
    Route::get('anticretic_report/pdf/{total}/{search}', [ExportController::class, 'AnticreticReport']);
    Route::get('anticretic_report/pdf/{total}', [ExportController::class, 'AnticreticReport']);
    Route::get('bill_report/pdf/{total}/{search}', [ExportController::class, 'BillReport']);
    Route::get('bill_report/pdf/{total}', [ExportController::class, 'BillReport']);
    Route::get('import_report/pdf/{total}/{search}', [ExportController::class, 'ImportReport']);
    Route::get('import_report/pdf/{total}', [ExportController::class, 'ImportReport']);
    Route::get('check_report/pdf/{total}/{search}', [ExportController::class, 'CheckReport']);
    Route::get('check_report/pdf/{total}', [ExportController::class, 'CheckReport']);
    Route::get('costumer_report/pdf/{total}/{search}', [ExportController::class, 'CostumerReport']);
    Route::get('costumer_report/pdf/{total}', [ExportController::class, 'CostumerReport']);
    Route::get('provider_report/pdf/{total}/{search}', [ExportController::class, 'ProviderReport']);
    Route::get('provider_report/pdf/{total}', [ExportController::class, 'ProviderReport']);
    Route::get('other_prov_report/pdf/{total}/{search}', [ExportController::class, 'OtherProvReport']);
    Route::get('other_prov_report/pdf/{total}', [ExportController::class, 'OtherProvReport']);
    Route::get('payable_report/pdf/{total}/{search}', [ExportController::class, 'PayableReport']);
    Route::get('payable_report/pdf/{total}', [ExportController::class, 'PayableReport']);
    Route::get('other_receivable_report/pdf/{total}/{search}', [ExportController::class, 'OtherReceivableReport'])->where('search','.*');
    Route::get('other_receivable_report/pdf/{total}', [ExportController::class, 'OtherReceivableReport']);
    Route::get('gym_report/pdf/{total}', [ExportController::class, 'GymReport']);
});