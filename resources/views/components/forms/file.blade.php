<div>
    <input type="file" wire:model.defer="{{ $model }}" class="form-control @error($model) is-invalid @enderror"
        id="{{ $model }}" placeholder="Masukkan {{ $name }}" />
    @error($model)
        <x-forms.error message="{{ $message }}" />
    @enderror
</div>
