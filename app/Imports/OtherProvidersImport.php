<?php

namespace App\Imports;

use App\Models\OtherProvider;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OtherProvidersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new OtherProvider([
            'reference' => $row['reference'],
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
