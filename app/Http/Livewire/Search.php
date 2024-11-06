<?php

namespace App\Http\Livewire;

use App\Models\Office;
use Livewire\Component;

class Search extends Component
{
    public $search,$office,$sale_price,$pf,$allowPrinting;

    public function mount()
    {
        $this->allowPrinting = true;
    }

    public function render()
    {
        return view('livewire.search',[
            'offices' => Office::orderBy('id', 'asc')->get(),
        ]);
    }

    public function changePrintingCheckboxStatus($isChecked)
    {
        if ($isChecked) {

            $this->allowPrinting = true;

        } else {

            $this->allowPrinting = false;

        }
    }
}