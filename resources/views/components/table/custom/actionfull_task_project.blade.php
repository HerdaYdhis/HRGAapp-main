<div class="d-flex justify-content-center">
    @if ($row->progress == 1)
        <button class="btn btn-light-success btn-sm me-2"
            wire:click.prevent="$emit('toDetailTask', {{ json_encode($row->id) }})">
            Detail Task
        </button>
        @if ($row->status != 'cancelled')
            <button class="btn btn-secondary btn-sm" type="button"
                wire:click="$emit('cancelled', {{ json_encode($row) }})"
                onclick="return confirm('Are you sure you want to cancel this task?')|| event.stopImmediatePropagation()">
                Cancelled
            </button>
        @endif
    @else
        <button class="btn btn-light-primary btn-sm me-2" wire:click="$emit('edit', {{ json_encode($row) }})">
            Edit
        </button>
    @endif
    @if (auth()->user()->is_superadmin)
        <button class="btn btn-danger btn-sm" type="button" wire:click="$emit('delete', {{ json_encode($row) }})"
            onclick="return confirm('Are you sure you want to delete this data?')|| event.stopImmediatePropagation()">
            Hapus
        </button>
    @endif

</div>
