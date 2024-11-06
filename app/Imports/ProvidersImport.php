<?php

namespace App\Imports;

use App\Models\Provider;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProvidersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Provider([
            'description' => $row['description'],
            'country' => $row['country'],
            'city' => $row['city']
        ]);
    }
}
