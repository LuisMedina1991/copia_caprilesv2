<?php

namespace App\Imports;

use App\Models\Bill;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BillsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Bill([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'type' => $row['type'],
            'amount' => $row['amount']
        ]);
    }
}
