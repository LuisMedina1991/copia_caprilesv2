<?php

namespace App\Imports;

use App\Models\OtherReceivable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OtherReceivablesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new OtherReceivable([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
