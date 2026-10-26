<div class="d-flex align-items-center">
    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
        <div class="symbol-label">
            <img src="https://icons.getbootstrap.com/assets/img/icons-hero@2x.png" alt="{{ $row->name }}"
                 class="w-100">
        </div>
    </div>
    <div class="d-flex flex-column">
        <p class="mb-1">
            {{ $row->name }}
        </p>
{{--        <span class="text-muted">{{ $row->code }}</span>--}}
    </div>
</div>
