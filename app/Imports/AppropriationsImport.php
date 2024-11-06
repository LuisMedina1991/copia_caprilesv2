<?php

namespace App\Imports;

use App\Models\Appropriation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AppropriationsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Appropriation([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
