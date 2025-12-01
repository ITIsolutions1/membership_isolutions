@extends('adminlte::page')

@section('content')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var referralModal = new bootstrap.Modal(document.getElementById('referralModal'));
        referralModal.show();
    });
</script>

<!-- Modal -->
<div class="modal fade" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="referralModalLabel">Masukkan Kode Referral</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('events.referral.submit', $event_id) }}" method="POST">
        @csrf

        <div class="modal-body">
            <input type="text" 
                   name="referral_code" 
                   class="form-control" 
                   placeholder="Masukkan kode referral (opsional)">
        </div>

     <div class="modal-footer">
    <button type="button" class="btn btn-secondary"
            onclick="window.location.href='{{ route('events.referral.skip', $event_id) }}'">
        Lewati
    </button>
    <button type="submit" class="btn btn-primary">Lanjutkan</button>
</div>

      </form>

    </div>
  </div>
</div>
@endsection
