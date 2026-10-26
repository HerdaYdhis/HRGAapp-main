<div x-data x-init="flatpickr($refs.input, {
    minDate: 'today',
    {{-- mode: 'range', --}}
    dateFormat: 'Y-m-d',
    onChange: function(selectedDates, dateStr, instance) {
        $wire.set('{{ $model }}', dateStr);
    },
});">
    <input type="text" x-ref="input" wire:model.defer="{{ $model }}"
        class="form-control @error($model) is-invalid @enderror flatpickr-time-picker-24h" id="{{ $model }}"
        placeholder="Masukkan {{ $name }}" />
</div>
