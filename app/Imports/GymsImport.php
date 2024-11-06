<?php

namespace App\Imports;

use App\Models\Gym;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GymsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Gym([
            'description' => $row['description'],
            'amount' => $row['amount']
        ]);
    }
}
