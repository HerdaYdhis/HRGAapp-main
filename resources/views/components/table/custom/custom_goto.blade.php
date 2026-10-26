<div class="d-flex">
    <button class="btn btn-light-success btn-sm me-2" wire:loading.attr="disabled"
        wire:click="$emit('input_nilai', {{ json_encode($row) }})">
        Indikator Kinerja
    </button>
</div>
