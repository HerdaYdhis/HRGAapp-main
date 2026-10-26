<div class="d-flex justify-content-center">
    <button class="btn btn-danger btn-sm" type="button" wire:click="$emit('delete', {{ json_encode($row) }})"
        onclick="return confirm('Are you sure you want to delete this data?')|| event.stopImmediatePropagation()">
        Delete
    </button>
</div>
