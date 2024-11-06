<script>

    document.addEventListener('DOMContentLoaded', function() {  //evento para scrollbar personalizada
        $('.tblscroll').niceScroll({
            
            cursorcolor: "#515365",
            cursorwidth: "30px",
            background: "rgba(20,20,20,0.3)",
            cursorborder: "0px",
            cursorborderradius: 3

        })
    })

    function Confirm(id, eventName, text){   //funcion para mensaje de confirmacion sweetalert 

        swal({
            title: 'CONFIRMAR',
            text: text,
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'CERRAR',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'ACEPTAR'
        }).then(function(result){
            if(result.value){   //validar si se presiono el boton de confirmacion
                window.livewire.emit(eventName, id) //emision de evento
                swal.close()    //cerrar alerta
            }
        })
    }

</script>