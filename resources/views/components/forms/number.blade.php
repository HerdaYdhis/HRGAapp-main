<div>
    <input type="number" wire:model.defer="{{ $model }}" class="form-control @error($model) is-invalid @enderror"
        id="{{ $model }}" placeholder="Masukkan {{ $name }}" step=".01" />
    @error($model)
        <x-forms.error message="{{ $message }}" />
    @enderror
</div>
