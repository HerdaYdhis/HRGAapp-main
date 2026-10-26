<style>
    .modal-body {
        overflow-y: auto !important;
        max-height: 400px !important;
    }
</style>
<div wire:ignore.self class="modal fade" tabindex="-1" aria-hidden="true" id="{{ $_id }}"
    aria-labelledby="{{ $_id }}Label">
    <div {{ $attributes->merge(['class' => 'modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable']) }}>
        <div class="modal-content">
            <div class="modal-header" id="{{ $_id }}_header">
                <h5 class="modal-title" id="{{ $_id }}Title">{{ $title }}
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            @if ($form)
                <form wire:submit.prevent="{{ $form }}">
                    <div class="modal-body">
                        {{ $content }}
                    </div>
                    <div class="modal-footer">
                        {{ $action }}
                    </div>
                </form>
            @else
                <div class="modal-body">
                    {{ $content }}
                </div>
                <div class="modal-footer">
                    {{ $action }}
                </div>
            @endif
            {{--            --}}
            {{--            <div class="modal-footer"> --}}
            {{--                <button type="button" class="btn btn-light-secondary" --}}
            {{--                        data-bs-dismiss="modal"> --}}
            {{--                    <i class="bx bx-x d-block d-sm-none"></i> --}}
            {{--                    <span class="d-none d-sm-block">Close</span> --}}
            {{--                </button> --}}
            {{--                <button type="button" class="btn btn-primary ml-1" data-bs-dismiss="modal"> --}}
            {{--                    <i class="bx bx-check d-block d-sm-none"></i> --}}
            {{--                    <span class="d-none d-sm-block">Accept</span> --}}
            {{--                </button> --}}
            {{--            </div> --}}
        </div>
    </div>
</div>
