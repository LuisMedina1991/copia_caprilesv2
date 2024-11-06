<?php

namespace App\Http\Livewire;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Cover;
use App\Models\Detail;
use App\Models\Paydesk;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BankAccountReports extends Component
{   

    public $componentName,$details,$reportRange,$bank_account_id,$now,$dateFrom,$dateTo,$date_field_1,$date_field_2;

    public function mount()
    {
        $this->componentName = 'MOVIMIENTOS BANCARIOS';
        $this->details = [];
        $this->reportRange = 0;
        $this->bank_account_id = 0;
        $this->date_field_1 = '';
        $this->date_field_2 = '';
        $this->now = Carbon::now();
        $this->dateFrom = $this->now->format('Y-m-d') . ' 00:00:00';
        $this->dateTo = $this->now->format('Y-m-d') . ' 23:59:59';
    }

    public function render()
    {   
        $this->ReportsByDate();

        return view('livewire.bank_account_report.bank-account-reports', [

            'bank_accounts' => BankAccount::with('company','bank')->get()
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }

    public function ReportsByDate()
    {
        if ($this->reportRange == 0) {

            $from = $this->now->format('Y-m-d') . ' 00:00:00';
            $to = $this->now->format('Y-m-d') . ' 23:59:59';

        } else {

            $from = $this->date_field_1. ' 00:00:00';
            $to = $this->date_field_2. ' 23:59:59';

        }

        if ($this->reportRange == 1 && ($this->date_field_1 == '' || $this->date_field_2 == '')) {

            $this->emit('report-error', 'Seleccione fecha de inicio y fecha de fin');
            $this->details = [];
            return;
        }

        if ($this->bank_account_id == 0) {

            /*$this->details = BankAccount::join('details as d','d.detailable_id','bank_accounts.id')
            ->select('d.*')
            ->whereBetween('d.created_at', [$from, $to])
            ->where('d.detailable_type','App\Models\BankAccount')
            ->orderBy('d.detailable_id')->get();*/

            $this->details = Detail::whereBetween('created_at', [$from, $to])
                ->where('detailable_type','App\Models\BankAccount')
                ->orderBy('detailable_id')
                ->get();

        } else {
            
            /*$this->details = BankAccount::join('details as d','d.detailable_id','bank_accounts.id')
                ->select('d.*')
                ->whereBetween('d.created_at', [$from, $to])
                ->where('d.detailable_id',$this->bank_account_id)
                ->where('d.detailable_type','App\Models\BankAccount')
                ->orderBy('d.id')
                ->get();*/

            $this->details = Detail::whereBetween('created_at', [$from, $to])
                ->where('detailable_type','App\Models\BankAccount')
                ->where('detailable_id',$this->bank_account_id)
                ->orderBy('id')
                ->get();

        }
    }

    protected $listeners = [

        'destroy' => 'Destroy'

    ];

    public function Destroy(Detail $detail)
    {
        if ( $this->reportRange != 0 ) {

            $this->emit('report-error', 'Solo se puede eliminar movimientos del dia.');
            return;

        }

        $bank_account = BankAccount::find($detail->detailable_id);

        if (!$bank_account) {

            $this->emit('report-error', 'No se ha encontrado la cuenta bancaria relacionada con este movimiento.');
            return;

        } else {

            /*$paydesk_bank_account_movements = Paydesk::where('type','deposito/retiro')->whereBetween('created_at',[$this->dateFrom, $this->dateTo])->get();

            if ( count($paydesk_bank_account_movements) > 0 ) {
    
                $target = $paydesk_bank_account_movements->firstWhere('relation',$detail->id);
    
                if ($target) {
    
                    $this->emit('report-error', 'El movimiento debe anularse desde caja.');
                    return;
    
                }
            }*/

            $bank_name = Bank::find($bank_account->bank_id)->description;
            $company_name = Company::find($bank_account->company_id)->description;
            $bank_account_cover = Cover::firstWhere('description',$bank_name . ' ' . $bank_account->type . ' ' . $bank_account->currency . ' ' . $company_name);

            if (!$bank_account_cover) {

                $this->emit('report-error', 'No se ha encontrado caratula para esta cuenta.');
                return;

            } else {

                $bank_account_cover_detail = $bank_account_cover->details->whereBetween('created_at',[$this->dateFrom, $this->dateTo])->first();

                if (!$bank_account_cover_detail) {

                    $this->emit('report-error', 'No se ha encontrado caratula del dia para esta cuenta.');
                    return;

                } else {

                    DB::beginTransaction();
                        
                    try {

                        if ($detail->actual_balance > $detail->previus_balance) {     //significa ingreso

                            if ( ($detail->actual_balance - $detail->amount) != ($bank_account->amount - $detail->amount) ) {

                                $this->emit('report-error', 'El saldo no coincide. Anule los movimientos mas recientes.');
                                return;

                            } else {

                                $bank_account->update([
                                
                                    'amount' => $bank_account->amount - $detail->amount

                                ]);

                                $bank_account_cover->update([

                                    'balance' => $bank_account_cover->balance - $detail->amount

                                ]);

                                $bank_account_cover_detail->update([

                                    'ingress' => $bank_account_cover_detail->ingress - $detail->amount,
                                    'actual_balance' => $bank_account_cover_detail->actual_balance - $detail->amount

                                ]);

                            }

                        } else {    //significa egreso

                            if ( ($detail->actual_balance + $detail->amount) != ($bank_account->amount + $detail->amount) ) {

                                $this->emit('report-error', 'El saldo no coincide. Anule los movimientos mas recientes.');
                                return;

                            } else {

                                $bank_account->update([
                            
                                    'amount' => $bank_account->amount + $detail->amount
                    
                                ]);
                    
                                $bank_account_cover->update([
                        
                                    'balance' => $bank_account_cover->balance + $detail->amount
                    
                                ]);
                    
                                $bank_account_cover_detail->update([
                    
                                    'egress' => $bank_account_cover_detail->egress - $detail->amount,
                                    'actual_balance' => $bank_account_cover_detail->actual_balance + $detail->amount
                    
                                ]);

                            }
                        }

                        $detail->delete();
                        DB::commit();
                        $this->emit('report-error', 'Movimiento Anulado.');
                        $this->mount();
                        $this->render();

                    } catch (Exception $e) {

                        DB::rollback();
                        //$this->emit('report-error', $e->getMessage());
                        $this->emit('report-error', 'Algo salio mal.');

                    }
                }
            }
        }
    }
}
