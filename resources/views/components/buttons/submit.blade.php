<button type="submit" {{ $attributes->merge(['class' => 'btn']) }} wire:target="{{ $target }}" wire:loading.class="spinner spinner-light spinner-right"
        wire:offline.attr="disabled">
    <div wire:loading.remove wire:target="{{ $target }}">
        {{ $label }}
    </div>
    <div wire:loading wire:target="{{ $target }}">
        Tunggu Sebentar...
        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
    </div>
</button>
