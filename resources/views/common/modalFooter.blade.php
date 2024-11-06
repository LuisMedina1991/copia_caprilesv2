</div>
<div class="modal-footer">
  <!--directiva de livewire que hace llamado a metodo del controlador-->
  <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
  @if ($selected_id < 1)  <!--validar si registro ya existe para definir que boton mostrar-->
    <!--directiva de livewire que hace llamado a metodo del controlador-->
    <button type="button" wire:click.prevent="Store()" class="btn btn-dark close-modal">GUARDAR</button>
  @else
    <!--directiva de livewire que hace llamado a metodo del controlador-->
    <button type="button" wire:click.prevent="Update()" class="btn btn-dark close-modal">ACTUALIZAR</button>
    {{--<button type="button" wire:click.prevent="Update('{{$stock->office_id}}')" class="btn btn-dark close-modal">ACTUALIZAR</button>--}}
  @endif
</div>
</div>
</div>
</div>