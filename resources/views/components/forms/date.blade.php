<div>
    <input type="date" wire:model.defer="{{ $model }}" class="form-control @error($model) is-invalid @enderror"
        id="{{ $model }}" placeholder="Masukkan {{ $name }}" />
    @error($model)
        <x-forms.error message="{{ $message }}" />
    @enderror
    <!-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie -->
</div>
