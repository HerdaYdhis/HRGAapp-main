<tr>
    <td>{{ $model->id }}</td>
    <td>{{ $model->indikator }}</td>
    <td>{{ $model->bobot }}</td>
    <td>
        @foreach ($model->evaluations as $evaluation)
            {{ $evaluation->nilai }}
        @endforeach
    </td>
</tr>
