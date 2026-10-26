@if ($row != 'N/A')
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: {{ $row }}%" aria-valuenow="{{ $row }}"
            aria-valuemin="0" aria-valuemax="100"></div>
    </div> {{ $row }}%
@else
    -
@endif
