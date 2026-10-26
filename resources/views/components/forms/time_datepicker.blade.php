<div>
    {{-- <input type="time" wire:model.defer="{{ $model }}"
        class="form-control @error($model) is-invalid @enderror flatpickr-time-picker-24h" id="{{ $model }}"
        placeholder="Masukkan {{ $name }}" /> --}}


    <div x-data x-init="flatpickr($refs.input, {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
        onChange: function(selectedDates, dateStr, instance) {
            $wire.set('{{ $model }}', dateStr);
        },
        onClose: function(selectedDates, dateStr, instance) {
            $wire.set('{{ $model }}', dateStr);
        }
    });">
        <input type="text" x-ref="input" wire:model.defer="{{ $model }}"
            class="form-control @error($model) is-invalid @enderror" id="{{ $model }}"
            placeholder="Masukkan {{ $name }}" />
        @error($model)
            <x-forms.error message="{{ $message }}" />
        @enderror
    </div>
</div>
