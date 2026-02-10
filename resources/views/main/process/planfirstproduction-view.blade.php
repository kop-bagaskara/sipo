@extends('main.layouts.main')
@section('content')
<h4>Jadwal Plan Mesin</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mesin</th>
            @foreach(array_keys(reset($grouped)) as $tgl)
                <th>{{ $tgl }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($grouped as $mesin => $byDate)
            <tr>
                <td>{{ $mesin }}</td>
                @foreach($byDate as $tgl => $items)
                    <td>
                        @foreach($items as $item)
                            <div>
                                <b>{{ $item->materialcode }}</b><br>
                                Qty: {{ $item->qty }}<br>
                                {{ $item->start_jam }} - {{ $item->end_jam }}
                            </div>
                            <hr>
                        @endforeach
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@endsection