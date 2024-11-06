<?php

namespace App\Imports;

use App\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportsImport implements ToModel ,WithHeadingRow
{
    public function model(array $row)
    {
        return new Import([
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
