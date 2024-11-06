<?php

namespace App\Imports;

use App\Models\Anticretic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AnticreticsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Anticretic([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
