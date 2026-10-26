<div>
    <fieldset class="form-group">
        <select class="form-select @error($model) is-invalid @enderror" id="{{ $model }}" wire:model="{{ $model }}">
            <option value="">Pilih {{ $name }}</option>
            @forelse ($datas as $key => $item)
                <option value="{{ $key }}">{{ $item }}</option>
            @empty
                <option value="" disabled>{{ $name }} Tidak Ditemukan</option>
            @endforelse
        </select>
        @error($model)
        <x-forms.error message="{{ $message }}" />
        @enderror
    </fieldset>
</div>
