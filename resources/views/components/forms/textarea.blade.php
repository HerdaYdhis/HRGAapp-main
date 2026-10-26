<div>
    <textarea wire:model.defer="{{ $model }}" class="form-control @error($model) is-invalid @enderror"
        id="{{ $model }}" placeholder="Masukkan {{ $name }}" rows="6"></textarea>
    @error($model)
        <x-forms.error message="{{ $message }}" />
    @enderror
</div>
