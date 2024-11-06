<div>

    <style></style>

    <div class="row layout-top-spacing">

        <div class="col-sm-12 col-md-8">
            <!--DETALLES-->
            @include('livewire.sale.partials.detail')
        </div>

        <div class="col-sm-12 col-md-4">
            <!--TOTAL-->
            @include('livewire.sale.partials.total')

            <!--COINS-->
            @include('livewire.sale.partials.coins')

        </div>

    </div>

</div>

@include('livewire.sale.scripts.events')
@include('livewire.sale.scripts.general')
{{--@include('livewire.pos.scripts.scan')--}}
{{--@include('livewire.sale.scripts.shortcuts')--}}
