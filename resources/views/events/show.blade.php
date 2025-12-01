
@extends('adminlte::page')

@section('title', $event->nama)

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="row no-gutters">
            <!-- Gambar -->
            @if(!empty($event->gambar) && $event->gambar != '')
                <div class="col-md-5">
                    <img src="{{ asset('storage/' . $event->gambar) }}" class="card-img rounded-left" alt="{{ $event->nama }}" style="object-fit: cover;">
                </div>
            @else
                <div class="col-md-5">
                    <img src="{{ asset('images/no_image.png') }}" class="card-img h-100 rounded-left" alt="Default Event" style="object-fit: cover;">
                </div>
            @endif

            <!-- Konten -->
            <div class="col-md-7">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="font-weight-bold text-primary">{{ $event->nama }}</h5>
                        <div>
                            <button class="btn btn-success"  onclick="copyEventLink('{{ route('events.show2', $event->id) }}')" >Share Event Link</button>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Deskripsi:</strong></p>
                    {!! $event->deskripsi !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card shadow-lg border-0 rounded-lg {{ Auth::user()->role == 'admin' ? 'col-5' : 'col' }} mx-3 p-0">
            <div class="card-header bg-secondary text-white d-flex align-items-center">
                <strong>Detail Informasi</strong>
            </div>
            <div class="card-body">
                    {{-- <div>
                        <p class="text-muted mb-2"><i class="fas fa-user-tie"></i> Oleh: <strong>{{ $event->createdBy }}</strong></p>
                        <p class="text-muted mb-2"><i class="fas fa-user-tie"></i> Narasumber: <strong>{{ $event->narasumber }}</strong></p>
                        <p class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> Lokasi: <strong>{{ $event->Lokasi }}</strong></p>
                        <p class="text-muted mb-2"><i class="fas fa-calendar-alt"></i> Tanggal: <strong>{{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}</strong></p>
                        <p class="text-muted mb-2"><i class="fas fa-clock"></i> Jam: <strong>{{ $event->waktu}}</strong></p>
                        <p class="text-muted mb-3"><i class="fas fa-file"></i> Jenis Acara: <strong>{{ $event->jenis_peminatan}}</strong></p>
                    </div> --}}

                    <div class="table-responsive">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                {{-- <tr>
                                    <td class="align-top text-muted"><i class="fas fa-user-tie"></i></td>
                                    <td class="align-top text-muted">Oleh</td>
                                    <td class="align-top">: <strong>{{ $event->createdBy }}</strong></td>
                                </tr> --}}
                                <tr>
                                    <td class="align-top text-muted"><i class="fas fa-user-tie"></i></td>
                                    <td class="align-top text-muted">Narasumber</td>
                                    <td class="align-top text-muted">:</td>
                                    <td class="align-top"><strong>{{ $event->narasumber }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="align-top text-muted"><i class="fas fa-map-marker-alt"></i></td>
                                    <td class="align-top text-muted">Lokasi</td>
                                    <td class="align-top text-muted">:</td>
                                    <td class="align-top"><strong>{{ $event->lokasi }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="align-top text-muted"><i class="fas fa-calendar-alt"></i></td>
                                    <td class="align-top text-muted">Tanggal</td>
                                    <td class="align-top text-muted">:</td>
                                    <td class="align-top"><strong>{{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="align-top text-muted"><i class="fas fa-clock"></i></td>
                                    <td class="align-top text-muted">Jam</td>
                                    <td class="align-top text-muted">:</td>
                                    <td class="align-top"><strong>{{ $event->waktu }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="align-top text-muted"><i class="fas fa-file"></i></td>
                                    <td class="align-top text-muted">Jenis Acara</td>
                                    <td class="align-top text-muted">:</td>
                                    <td class="align-top"><strong>{{ $event->jenis_peminatan }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div id="status_pendaftaran" class="status_pendaftaran">
                        @if($terdaftar == true)
                            <h3><span class="badge badge-success">Anda Telah Terdaftar</span></h3>
                            <a class="btn btn-outline-danger" href="{{ route('events.register.batalkan', $event->id) }}">Batalkan Pendaftaran</a>
                        @endif
                    </div>

                    <div id="tombol" class="tombol">
                        @if($event->link)
                        <a href="{{ $event->link }}" target="_blank" class="btn btn-outline-primary btn-block mt-3">
                            <i class="fas fa-external-link-alt"></i> Link Event
                        </a>
                        @endif

                        <!-- <div>
                            <a href="{{ route('events.register', $event->id) }}" class="btn btn-outline-primary btn w-100 mt-3">Daftar Event</a>
                        </div> -->
                        <a href="{{ route('events.referral.check', $event->id) }}" class="btn btn-outline-primary btn w-100 mt-3">
                            Daftar Event
                        </a>


                        @if(Auth::user()->role == 'admin')
                            <div>
                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-danger btn w-100 mt-3">Edit</a>
                            </div>
                        @endif
                    </div>
            </div>
        </div>
        @if(Auth::user()->role == 'admin')
        <div class="card shadow-lg border-0 rounded-lg col p-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <strong>Daftar Peserta</strong>
                <strong>Jumlah Peserta : {{ $peserta->count() }}</strong>
            </div>
            <div class="card-body">
                <div>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>kode Referal</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peserta as $index => $item)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->nomor }}</td>
                               <td>
                                    @if ($item->pivot && $item->pivot->referred_by)
                                        {{ str_pad($item->pivot->referred_by, 6, '0', STR_PAD_LEFT) }}
                                    @else
                                        -
                                    @endif
                                </td>


                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>

</div>

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Pendaftaran Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    <script>
        function copyEventLink(link) {
            navigator.clipboard.writeText(link)
            .then(() => {
                alert("Link berhasil disalin: " + link);
            })
            .catch(err => {
                console.error("Gagal menyalin: ", err);
            });
        }
    </script>
@endpush
@endsection
