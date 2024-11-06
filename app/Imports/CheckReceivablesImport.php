<?php

namespace App\Imports;

use App\Models\CheckReceivable;
use App\Models\Costumer;
use App\Models\Bank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CheckReceivablesImport implements ToModel, WithHeadingRow
{
    private $costumers,$banks;

    public function __construct()
    {
        $this->costumers = Costumer::pluck('id','description');
        $this->banks = Bank::pluck('id','description');
    }

    public function model(array $row)
    {
        return new CheckReceivable([
            'description' => $row['description'],
            'amount' => $row['amount'],
            'number' => $row['number'],
            'bank_id' => $this->banks[$row['bank_id']],
            'costumer_id' => $this->costumers[$row['costumer_id']]
        ]);
    }
}
