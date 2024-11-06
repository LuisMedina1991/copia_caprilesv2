<?php

namespace App\Imports;

use App\Models\Provider;
use App\Models\ProviderPayable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProviderPayablesImport implements ToModel, WithHeadingRow
{
    private $providers;

    public function __construct()
    {
        /*obtencion de datos opcion 1*/
        //$this->providers = Provider::select('id','description')->get();
        /*obtencion de datos opcion 2*/
        $this->providers = Provider::pluck('id','description');
    }

    public function model(array $row)
    {
        /*necesario obtener registro primero usando opcion 1*/
        //$provider = $this->providers->where('description',$row['provider_id'])->first();

        return new ProviderPayable([
            'description' => $row['description'],
            'amount' => $row['amount'],
            /*obtencion de id usando opcion 1*/
            //'provider_id' => $provider->id
            /*obtencion de id usando opcion 2*/
            'provider_id' => $this->providers[$row['provider_id']]
        ]);
    }
}
