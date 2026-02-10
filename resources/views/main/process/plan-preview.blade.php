@extends('layouts.app')

@section('content')
<div class="modal fade show" id="planPreviewModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.2);" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-xl draggable">
    <div class="modal-content">
      <div class="modal-header cursor-move">
        <h5 class="modal-title">Preview Timeline Rencana Produksi</h5>
        <button type="button" class="close" onclick="window.history.back();">&times;</button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <b>Keterangan:</b> <span class="badge badge-primary">Bar warna</span> = durasi proses, <span class="badge badge-info">Hover</span> untuk detail waktu.
        </div>
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle" style="min-width:1200px;">
            <thead class="thead-dark">
              <tr>
                <th style="min-width:120px;">KODE ITEM</th>
                @php
                  $allProses = collect($planPerItem)->flatMap(function($row){return collect($row)->pluck('Proses');})->unique()->values();
                @endphp
                @foreach($allProses as $proses)
                  <th style="min-width:180px;">{{ $proses }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($planPerItem as $itemCode => $prosesList)
                <tr>
                  <td class="text-left align-middle">
                    <b>{{ $itemCode }}</b><br>
                    <small>{{ $prosesList[0]['MaterialName'] ?? '' }}</small>
                  </td>
                  @foreach($allProses as $proses)
                    @php
                      $cell = collect($prosesList)->firstWhere('Proses', $proses);
                    @endphp
                    <td style="vertical-align:middle;">
                      @if($cell)
                        <div class="timeline-bar" style="background:#007bff; height:18px; border-radius:4px; position:relative; margin-bottom:4px;"
                          title="{{ $cell['StartJam'] }} s/d {{ $cell['EndJam'] }}">
                          <span style="position:absolute; left:4px; top:0; color:#fff; font-size:11px;">{{ \Carbon\Carbon::parse($cell['StartJam'])->format('d/m H:i') }}</span>
                          <span style="position:absolute; right:4px; top:0; color:#fff; font-size:11px;">{{ \Carbon\Carbon::parse($cell['EndJam'])->format('d/m H:i') }}</span>
                        </div>
                        <div style="font-size:12px;">
                          <b>Qty:</b> {{ $cell['Quantity'] }}<br>
                          <b>Est:</b> {{ number_format($cell['Estimation'],2) }} jam
                        </div>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <form method="POST" action="{{ url('/plan/save') }}">
          @csrf
          <input type="hidden" name="plan_data" value="{{ base64_encode(json_encode($planPerItem)) }}">
          <button type="submit" class="btn btn-success">Simpan ke Database</button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Kembali/Edit</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function(){
  $('.modal-dialog.draggable').draggable({ handle: '.modal-header' });
});
</script>
<style>
.timeline-bar { position: relative; background: #007bff; }
.modal-xl { max-width: 98vw; }
</style>
@endpush
