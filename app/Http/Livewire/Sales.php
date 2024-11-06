<?php

namespace App\Http\Livewire;

use App\Models\Denomination;
use App\Models\Product;
use App\Models\Office;
use App\Models\Sale;
use App\Models\State;
use App\Models\Cover;
use App\Models\Paydesk;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;    //conector del paquete especifico para windows
use Mike42\Escpos\Printer;  //instancia de la clase que permite el envio de la impresion
use Mike42\Escpos\EscposImage;  //clase del paquete para poder imprimir imagenes
use Mike42\Escpos\PrintConnectors\FilePrintConnector;   //clase del paquete para manejar las interfaces de conexion

class Sales extends Component
{
    public $total,$itemsQuantity,$efectivo,$change;
    public $cov,$cov_det,$from,$to;

    public function mount()
    {
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->from = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 00:00:00';
        $this->to = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        $this->cov = Cover::firstWhere('description','inventario');
        $this->cov_det = $this->cov->details->where('cover_id',$this->cov->id)->whereBetween('created_at',[$this->from, $this->to])->first();
    }

    public function render()
    {
        return view('livewire.sale.sales',[

            'denominations' => Denomination::orderBy('id', 'asc')->get(),
            'cart' => Cart::getContent()->sortBy('name')
        ])
        ->extends('app')
        ->section('content');
    }

    public function ACash($value){  //metodo para acumular el valor al clickear las botonos de denominaciones
        //sumatoria de lo que tiene en $efectivo + lo que se vaya clickeando
        //si se clickea exacto(0) la caja $efectivo muestra el valor de $total
        $this->efectivo += ($value == 0 ? $this->total : $value);   //caso contrario muestra el acumulativo de $value
        $this->change = ($this->efectivo - $this->total);   //obtenemos el cambio
    }

    protected $listeners = [

        'scan-code' => 'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'saveSale' => 'saveSale'
    ];

    
    public function ScanCode($code,$sale_price,$office,$pf,$cant = 1)
    {
        if ( (empty($code)) || (empty($sale_price)) || (empty($pf)) ) {

            $this->emit('scan-notfound', 'Rellene todos los campos');
            return;

        } else {

            $product = Product::firstWhere('code', $code);

            if ( (!$product) || (!$product->offices()->firstWhere('name',$office)) ){
    
                $this->emit('scan-notfound', 'El producto no esta registrado');
                return;
    
            } else {
                
                if ($this->InCart($product->offices()->first()->pivot->id, $office)) {
    
                    $this->increaseQty($product->offices()->first()->pivot->id);
                    return;
                }
    
                if ($product->offices()->firstWhere('name',$office)->pivot->stock < 1) {
    
                    $this->emit('no-stock','Stock insuficiente');
                    return;
                }
    
                //Cart::add($product->offices()->firstWhere('name',$office)->pivot->id,$office,$sale_price,$cant,array($product->image,$product->description,$product->id,$product->brand,$pf));
                //esta es la forma simple de agregar items al carrito segun la documentacion, el metodo add() recibe 5 parametros
                //el 1er parametro se guardara como 'id', el 2do parametro se guardara como 'name', el 3er parametro se guardara como 'price',
                //el 4to parametro se guardara como 'quantity', el 5to parametro es un array que puede aceptar muchos campos y se guardaran como 'attributes[0,etc]'
                Cart::add(
                    $product->offices()->firstWhere('name',$office)->pivot->id,
                    $office,
                    $sale_price,
                    $cant,
                    array(
                        $product->description,
                        $product->brand,
                        $product->threshing,
                        $product->tarp,
                        $product->id
                    )
                );

                $this->total = Cart::getTotal();
                $this->itemsQuantity = Cart::getTotalQuantity();
                $this->efectivo = 0;
                $this->change = 0;
                $this->emit('scan-ok', 'Producto agregado al carrito');
                
            }
        }
    }

    //metodo para verificar si un producto ya existe en el carrito
    public function InCart($id, $office)
    {
        //metodo get obtiene un item por su id, de no ser encontrado devuelve null
        $exist = Cart::get($id); //obtener item del carrito y lo guardamos en variable

        //validar si el id del producto en el carrito es el mismo id de office_product
        if ( ($exist) && ($exist->name == $office) ) {

            return true;

        } else {

            return false;

        }
    }

    public function increaseQty($cart_id, $cant = 1)
    {
        $title = '';
        $exist = Cart::get($cart_id);
        $office = Office::firstWhere('name',$exist->name);
        
        if ($exist) {

            $title = 'Cantidad Actualizada';

        } else {

            $title = 'Producto Agregado';

        }

        if ($exist) {

            if ($office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->stock < ($cant + $exist->quantity) ) {

                $this->emit('no-stock', 'Stock insuficiente');
                return;

            }
        }

        Cart::update($exist->id,array('quantity'=> $cant));
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->efectivo = 0;
        $this->change = 0;
        $this->emit('scan-ok', $title);
    }

    public function decreaseQty($cart_id)
    {
        $exist = Cart::get($cart_id);
        $office = Office::firstWhere('name',$exist->name);
        Cart::remove($cart_id);
        $newQty = ($exist->quantity) - 1;
        $threshing = $office->products()->get()->firstWhere('pivot.id',$exist->id)->threshing;
        $tarp = $office->products()->get()->firstWhere('pivot.id',$exist->id)->tarp;
        //$image = $office->products()->get()->firstWhere('pivot.id',$exist->id)->image;
        $description = $office->products()->get()->firstWhere('pivot.id',$exist->id)->description;
        $product_id = $office->products()->get()->firstWhere('pivot.id',$exist->id)->id;
        $brand = $office->products()->get()->firstWhere('pivot.id',$exist->id)->brand;

        if ($newQty > 0) {

            //Cart::add($office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->id, $exist->name, $exist->price, $newQty, array($image,$description,$product_id,$brand,$exist->attributes[4]));
            Cart::add(
                $office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->id,
                $exist->name,
                $exist->price,
                $newQty,
                array(
                    $description,
                    $brand,
                    $threshing,
                    $tarp,
                    $product_id
                )
            );

            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();
            $this->efectivo = 0;
            $this->change = 0;
            $this->emit('scan-ok', 'Cantidad actualizada');

        } else {

            $this->removeItem($cart_id);
            $this->emit('scan-ok', 'Producto eliminado del carrito');

        }
    }


    public function updateQty($cart_id, $cant = 1)
    {
        $title = '';
        $exist = Cart::get($cart_id);
        $office = Office::firstWhere('name',$exist->name);
        $threshing = $office->products()->get()->firstWhere('pivot.id',$exist->id)->threshing;
        $tarp = $office->products()->get()->firstWhere('pivot.id',$exist->id)->tarp;
        //$image = $office->products()->get()->firstWhere('pivot.id',$exist->id)->image;
        $description = $office->products()->get()->firstWhere('pivot.id',$exist->id)->description;
        $product_id = $office->products()->get()->firstWhere('pivot.id',$exist->id)->id;
        $brand = $office->products()->get()->firstWhere('pivot.id',$exist->id)->brand;

        if ($exist) {

            $title = 'Cantidad Actualizada';

        } else {

            $title = 'Producto Agregado';

        }

        if ($exist) { //validar si se obtuvo un valor distinto de null para el id buscado

            if ($office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->stock < $cant) {    //validar si las existencias del producto son suficientes

                $this->emit('no-stock', 'Stock insuficiente');  //evento a ser escuchado desde el frontend
                return; //detener el flujo del proceso

            }
        }

        $this->removeItem($cart_id);  //eliminar producto del carrito

        if ($cant > 0) {  //validar la cantidad

            //metodo add agrega o actualiza item en el carrito de compras
            //aqui obtenemos el id de office_product a travez de la relacion entre offices y products
            //Cart::add($office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->id, $exist->name, $exist->price, $cant, array($image,$description,$product_id,$brand,$exist->attributes[4]));
            Cart::add(
                $office->products()->get()->firstWhere('pivot.id',$exist->id)->pivot->id,
                $exist->name,
                $exist->price,
                $cant,
                array(
                    $description,
                    $brand,
                    $threshing,
                    $tarp,
                    $product_id
                )
            );
            //Cart::update($exist->id,array('quantity'=> $cant));
            $this->total = Cart::getTotal();    //actualizar el total con el metodo getTotal del carrito de compras
            $this->itemsQuantity = Cart::getTotalQuantity();    //actualizar la cantidad de item con el metodo getTotalQuantity del carrito de compras
            $this->efectivo = 0;
            $this->change = 0;
            $this->emit('scan-ok', $title); //evento a ser escuchado desde el frontend
        }
    }

    //metodo para eliminar item del carrito
    public function removeItem($cart_id)
    {
        Cart::remove($cart_id);   //metodo remove del carrito que recibe el id del producto y lo elimina
        $this->total = Cart::getTotal();    //actualizar el total con el metodo getTotal del carrito de compras
        $this->itemsQuantity = Cart::getTotalQuantity();    //actualizar la cantidad de item con el metodo getTotalQuantity del carrito de compras
        $this->efectivo = 0;
        $this->change = 0;
        $this->emit('scan-ok', 'Producto eliminado del carrito');   //evento a ser escuchado desde el frontend
    }

    //metodo para limpiar el carrito y reinicializar propiedades publicas
    public function clearCart()
    {
        Cart::clear();  //metodo clear de carrito para eliminar items del carrito
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = Cart::getTotal();    //actualizar el total con el metodo getTotal del carrito de compras
        $this->itemsQuantity = Cart::getTotalQuantity();    //actualizar la cantidad de item con el metodo getTotalQuantity del carrito de compras
        $this->emit('scan-ok', 'Carrito vacio');    //evento a ser escuchado desde el frontend
    }

    public function saveSale($allowPrinting,$receiptNumber)
    {
        if ($this->cov_det != null) {

            $paydesk = Paydesk::orderBy('id', 'asc')->whereBetween('created_at', [$this->from, $this->to])->where('type','Ventas')->get();

            if (count($paydesk) == 0) {

                if ($this->total <= 0) {

                    $this->emit('sale-error', 'AGREGA PRODUCTOS AL CARRITO');
                    return;

                }

                if ($this->efectivo <= 0) {

                    $this->emit('sale-error', 'INGRESE EL EFECTIVO');
                    return;

                }

                if ($this->total > $this->efectivo) {

                    $this->emit('sale-error', 'EL EFECTIVO ES INSUFICIENTE PARA LA COMPRA');
                    return;
                    
                }

                if (empty($receiptNumber)) {

                    $this->emit('sale-error', 'INGRESE EL NÚMERO DE PROFORMA');
                    return;

                }

                DB::beginTransaction();
                
                try {

                    $items = Cart::getContent();
                    $state = State::firstWhere('name','realizado');
                    $sale_details = [];

                    foreach ($items as $item) {

                        $product = Product::find($item->attributes[4]);
                        
                        $sale = Sale::create([

                            'quantity' => $item->quantity,
                            'total' => ($item->price * $item->quantity),
                            'utility' => (($item->price * $item->quantity) - ($product->cost * $item->quantity)),
                            'cash' => $this->efectivo,
                            'change' => $this->change,
                            'office' => $item->name,
                            'pf' => $receiptNumber,
                            'state_id' => $state->id,
                            'user_id' => Auth()->user()->id,
                            'product_id' => $product->id

                        ]);
                        
                        $product->offices()->updateExistingPivot($product->offices()->get()->firstWhere('pivot.id',$item->id)->pivot->office_id,[
                            'stock' => $product->offices()->get()->firstWhere('pivot.id',$item->id)->pivot->stock - $item->quantity,
                        ]);

                        if ($sale) {

                            $sale_details[] = [
                                'quantity' => $sale->quantity,
                                'product' => $sale->product->description,
                                'brand' => $sale->product->brand,
                                'branch_office' => $sale->office,
                                'sale_price' => $item->price,
                                'subtotal' => $sale->total,
                            ];

                        }
                    }

                    DB::commit();
                    $this->printSalesReceipt($allowPrinting,$receiptNumber,$sale_details,$this->efectivo,$this->change);
                    Cart::clear();
                    $this->efectivo = 0;
                    $this->change = 0;
                    $this->total = Cart::getTotal();
                    $this->itemsQuantity = Cart::getTotalQuantity();
                    $this->emit('sale-ok', 'Venta registrada con exito');

                } catch (Exception $e) {    //capturar error en variable 

                    DB::rollback();     //deshacer todas las operaciones en caso de error
                    $this->emit('sale-error', $e->getMessage());    //evento a ser escuchado desde el frontend

                }

            } else {

                $this->emit('sale-error', 'Anule las ventas del dia desde caja general primero');
                return;

            }

        } else {

            $this->emit('sale-error', 'Se debe crear caratula del dia');
            return;

        }
    }

    //metodo para imprimir tickets de venta
    public function printSalesReceipt($allowPrinting,$receiptNumber,$saleDetails,$cash,$change)
    {
        if ($allowPrinting) {

            $numberOfImpressions = 2;   //variable estatica para indicar la cantidad de impresiones que se realizaran

            for ($i = 1; $i <= $numberOfImpressions; $i++) {    //bucle para realizar la impresion acorde a la cantidad definida anteriormente

                $printerName = "EPSON TM-T20";    //nombre de la impresora con la que se hara conexion
                $connector = new WindowsPrintConnector($printerName);   //conector que recibe como unico parametro el nombre de la impresora con la que se hara conexion
                //instancia de la clase que enviara la impresion, recibe como 1er parametro el conector hacia la impresora
                $printer = new Printer($connector);
                $total = 0; //variable para acumular los subtotales de cada venta que dara como resultado el total de todas las venta
        
                //informacion y personalizacion del ticket
                //setJustification() es el metodo para justificar el texto en el eje x
                //recibe como parametro una de las siguientes opciones Printer::JUSTIFY_CENTER, Printer::JUSTIFY_LEFT o Printer::JUSTIFY_RIGHT
                //esta justificacion aplica a todo texto que siga a esta linea de codigo a menos que se sobreescriba mas abajo
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                //setTextSize() es el metodo para asignar un tamaño al texto
                //recibe 2 parametros (ancho del texto, alto del texto) ambos con un rango de 1-8 lo normal es (1,1)
                //este tamaño aplica a todo texto que siga a esta linea de codigo a menos que se sobreescriba mas abajo
                $printer->setTextSize(1,1);
                //text() es el metodo para añadir texto, el texto debe ir seguido de un salto de linea ("texto cualquiera\n")
                //o debe hacerse llamado al metodo feed() que imprime y hace saltos de linea, recibe como parametro el numero de saltos de linea
                //o por defecto se aplica un interlineado
                $printer->text("Proforma: " . $receiptNumber . "\n"); //numero de proforma
                $printer->feed();
                $printer->setTextSize(2,2);
                $printer->text("IMPORTADORA CAPRILES\n"); //titulo
                $printer->feed();
                $printer->setTextSize(1,1);
                $printer->text("================================================\n");   //interlineado
                $printer->text("Fecha: " . Carbon::now()->format('d/m/Y H:i:s') . "\n");    //fecha y hora de la venta
                $printer->text("================================================\n");   //interlineado
                $printer->setJustification(Printer::JUSTIFY_LEFT);
        
                foreach($saleDetails as $sale_detail) {
    
                    $printer->text(
                        "Cant:" . $sale_detail['quantity'] . " | " .
                        "Desc:" . $sale_detail['product'] . "-" . $sale_detail['brand'] . "\n"
                    );
    
                    $printer->text(
                        "Suc:" . $sale_detail['branch_office'] . " | " .
                        "Prec:" . number_format($sale_detail['sale_price'],2) . " | " .
                        "Sub:" . number_format($sale_detail['subtotal'],2) . "\n"
                    );
    
                    $total += $sale_detail['subtotal'];
        
                }
        
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text("================================================\n");   //interlineado
                $printer->text("Total: " . number_format($total,2) . "\n"); //sumatoria total de todas las ventas
                $printer->text("Pago: " . number_format($cash,2) . "\n");   //cantidad pagada por el cliente
                $printer->text("Cambio: " . number_format($change,2) . "\n");   //cambio resultante del pago
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su preferencia\n"); //pie de pagina
                $printer->feed(3);
                //cut() es el metodo para cortar el papel, puede recibir 2 parametros
                //como 1er parametro una de las opciones Printer::CUT_FULL que corta completamente o Printer::CUT_PARTIAL que corta segmentado
                //en caso de no especificar el 1er parametro se aplica Printer::CUT_FULL por defecto
                //como segundo parametro se indica el numero de saltos de linea
                $printer->cut();
                //close() es el metodo para cerrar la conexion con la impresora
                $printer->close();

            }
        }
    }
}
