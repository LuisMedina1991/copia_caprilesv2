<script>

    try{

        onScan.attachTo(document, {
            suffixKeyCodes: [13],
            onScan: function(barcode){
                console.log(barcode)
                window.livewire.emit('scan-code', barcode)
            },
            onScanError: function(e){
                console.log(e)
            }
        })

        console.log('Escaneo Correcto')

    }catch(e){
        console.log('Error de lectura: ', e)
    }

</script>