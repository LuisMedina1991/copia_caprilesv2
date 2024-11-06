<?php

namespace App\Imports;

use App\Models\Costumer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CostumersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Costumer([
            'description' => $row['description']
        ]);
    }
}
