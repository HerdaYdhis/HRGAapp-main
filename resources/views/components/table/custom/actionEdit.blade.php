<div class="d-flex justify-content-center">
    <button class="btn btn-light-info btn-sm me-2" wire:click="$emit('edit', {{ json_encode($row) }})">
        Edit
    </button>
</div>
