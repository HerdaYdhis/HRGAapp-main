<div class="d-flex justify-content-center">
    <button class="btn btn-light-success btn-sm me-2" wire:click="$emit('input_nilai', {{ json_encode($row) }})">
        Input Nilai
    </button>
</div>
