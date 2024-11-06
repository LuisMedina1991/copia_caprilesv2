<?php

namespace App\Imports;

use App\Models\Costumer;
use App\Models\CostumerReceivable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CostumerReceivablesImport implements ToModel, WithHeadingRow
{
    private $costumers;

    public function __construct()
    {
        $this->costumers = Costumer::pluck('id','description');
    }

    public function model(array $row)
    {
        return new CostumerReceivable([
            'description' => $row['description'],
            'amount' => $row['amount'],
            'costumer_id' => $this->costumers[$row['costumer_id']]
        ]);
    }
}
