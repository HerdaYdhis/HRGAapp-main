
<div class="d-flex justify-content-center">
    <button class="btn btn-light-primary btn-sm me-2" wire:click="$emit('edit', {{json_encode($row)}})">
        Edit
    </button>
    @if (auth()->user()->is_superadmin)
     <button class="btn btn-danger btn-sm" type="button" wire:click="$emit('delete', {{ json_encode($row) }})"
        onclick="return confirm('Are you sure you want to delete this data?')|| event.stopImmediatePropagation()">
        Hapus
    </button>
    @endif

</div>
