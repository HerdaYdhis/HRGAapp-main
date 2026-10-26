<div
    {{ $attributes->whereDoesntStartWith('wire:model')->merge([
        'class' => 'form-check form-check-custom
        form-check-solid',
    ]) }}>
    <input class="form-check-input" type="checkbox" id="{{ $label_id }}-checkbox"
        {{ $disabled ? ($disabled == true ? 'disabled' : '') : '' }}
        {{ $attributes->whereStartsWith('wire:model') }}, value="{{ $value }}" />
    <label class="form-check-label" for="{{ $label_id }}-checkbox">
        {{ $label }}
    </label>
</div>
