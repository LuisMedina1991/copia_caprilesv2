<script>

    document.addEventListener('DOMContentLoaded', function(){

        window.livewire.on('scan-ok', Msg => {  //evento para capturar todas las operaciones correctas con codigo de barras
            noty(Msg);
            $('#code').val('');
            $('#sale_price').val('');
        })

        window.livewire.on('sale-ok', Msg => {  //evento nuevo creado en fecha 02/11/2024 para reestablecer los valores del header al registrarse una venta
            noty(Msg);
            $('#code').val(''); //limpiamos el input del codigo del producto
            $('#sale_price').val('');   //limpiamos el input del precio de venta del producto
            $('#pf').val('');   //limpiamos el input del numero de proforma
            /*$('#office').val('central');    //asignamos el valor por defecto al select de la sucursal
            $('#printing_checkbox').val('1');   //asignamos el valor por defecto al checkbox de imprimir ticket
            $('#printing_checkbox').prop('checked', true);  //asignamos la propiedad checked al checkbox de imprimir ticket*/
        })

        window.livewire.on('scan-notfound', Msg => {    //evento para capturar todas las operaciones incorrectas con codigo de barras
            noty(Msg, 2)
        })

        window.livewire.on('no-stock', Msg => { //evento para stock insuficiente
            noty(Msg, 2)
        })

        window.livewire.on('sale-error', Msg => {   //evento para capturar errores al realizar venta
            noty(Msg, 2)
        })

        window.livewire.on('print-ticket', saleId => {  //evento para impresion de ticket de venta
            window.open("print://" + saleId , '_blank')
        })

    })

</script>