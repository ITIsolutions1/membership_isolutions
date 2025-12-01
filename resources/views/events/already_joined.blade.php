@extends('adminlte::page')

@section('content')

<style>
    /* Animasi icon ceklis */
    .success-check {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #28a74522;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        animation: pop 0.4s ease-out;
    }

    .success-check i {
        font-size: 40px;
        color: #28a745;
        animation: scaleIn 0.5s ease-in-out;
    }

    @keyframes pop {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes scaleIn {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
    });
</script>

<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Informasi</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">

        <!-- Ikon ceklis animasi -->
        <div class="success-check">
            <i class="fas fa-check"></i>
        </div>

        <h4 class="fw-bold mb-2">Anda Telah terdaftar Pada Event Ini!</h4>
        <p class="text-muted mb-0">
            Kamu telah terdaftar pada event ini. Silakan lanjut ke halaman event.
        </p>
      </div>

      <div class="modal-footer border-0 justify-content-center">
        <a href="{{ route('events.show', $event_id) }}" class="btn btn-success px-4">
            Lanjutkan
        </a>
      </div>

    </div>
  </div>
</div>

@endsection
