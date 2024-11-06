<?php

namespace App\Imports;

use App\Models\Payable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayablesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Payable([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
