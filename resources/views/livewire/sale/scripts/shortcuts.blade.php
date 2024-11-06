<script>

    document.addEventListener('DOMContentLoaded', function() {

        var listener = new window.keypress.Listener();  //inicializar variable para escuchar los eventos del teclado

        listener.simple_combo("f9", function(){ //evento para guardar la venta
            //console.log("f9")
            livewire.emit('saveSale')   //al escuchar la tecla se emite un evento a traves de livewire

        })

        listener.simple_combo("f8", function(){ //evento para limpiar la caja de texto $efectivo y posicionar el cursor ahi

            document.getElementById('cash').value = 0  //se limpia caja de texto
            document.getElementById('cash').focus()     //se posiciona el cursor en la caja de texto
            document.getElementById('hiddenTotal').value = 0

        })

        listener.simple_combo("f4", function(){ //evento para cancelar la venta

            //obtener el valor del input con id="hiddenTotal" declarado en la vista total y guardarlo en variable
            var total = parseFloat(document.getElementById('hiddenTotal').value)

            if(total > 0){  //validar que al menos exista un producto en carrito

                //en este caso la funcion Confirm recibe (id,accion/metodo/evento,mensaje)
                Confirm(0, 'clearCart', '¿SEGURO DE ELIMINAR EL CARRITO?')  //llamado al metodo js del archivo general.blade

            }else{

                noty('AGREGA PRODUCTOS AL CARRITO',2) //caso contrario se muestra este mensaje

            }

        })
    })

</script>