<?php

namespace App\Imports;

use App\Models\Office;
use App\Models\Product;
use App\Models\State;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    /*importacion productos + stock por sucursal*/
    /*se usa coleccion en lugar de modelo*/

    private $states,$offices;

    public function __construct()
    {
        $this->states = State::pluck('id','name');
        //$this->offices = Office::pluck('id','name');
        $this->offices = Office::select('id','name')->orderBy('id')->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $product = Product::create([

                'description' => $row['medida'],
                'code' => $row['codigo'],
                'brand' => $row['marca'],
                'ring' => $row['aro'],
                'threshing' => $row['trilla'],
                'tarp' => $row['lona'],
                'cost' => $row['costo'],
                'price' => $row['precio'],
                'category_subcategory_id' => $row['category_subcategory_id'],
                'state_id' => $this->states[$row['estado']]

            ]);

            /*foreach ($this->offices as $index => $office) {

                $product->offices()->attach($office,[
                    'stock' => $row[$index],
                    'alerts' => 1
                ]);

            }*/

            foreach ($this->offices as $office) {

                $product->offices()->attach($office->id,[
                    'stock' => $row[$office->name],
                    'alerts' => 1
                ]);

            }
        }
    }
}
