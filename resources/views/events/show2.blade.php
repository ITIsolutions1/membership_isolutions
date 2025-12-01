<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Event</title>
</head>
<body>
    <div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-lg">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

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


                    </div>
                    <hr>
                    <p><strong>Deskripsi:</strong></p>
                    {!! $event->deskripsi !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card shadow-lg border-0 rounded-lg col mx-3 p-0">
            <div class="card-header bg-secondary text-white d-flex align-items-center">
                <strong>Detail Informasi</strong>
            </div>
            <div class="card-body">


                    <div class="table-responsive">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>

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
                    <div class="tombol d-flex " style="width: 100%">
                        @if(Auth::check() == true)
                            <a href="{{ route('events.register', $event->id) }}" class="btn btn-outline-primary btn w-100 mt-3">Daftar Event</a>
                        @else
                            <button  type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#loginModal">
                                Daftar Event
                            </button>
                        @endif
                    </div>
                    <div id="tombol" class="tombol">
                        @if($event->link)
                        <a href="{{ $event->link }}" target="_blank" class="btn btn-outline-primary btn-block mt-3">
                            <i class="fas fa-external-link-alt"></i> Link Event
                        </a>
                        @endif
                    </div>

            </div>
        </div>

    </div>

</div>

<!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('register2') }}" method="POST">
                @csrf
            <div class="modal-body">
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama</label>
                        <input class="form-control" type="text" id="name" name="name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" type="email" id="email" name="email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nomor">Nomor Telepon</label>
                        <input class="form-control" type="text" id="nomor" name="nomor">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode Referal</label>
                        <input class="form-control" type="text" name="referral_code">
                    </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button class="btn btn-success" type="submit">Daftar</button>
                @error('email')
                <a class="btn btn-danger" href="{{ route('login', ['event' => $event->id]) }}">Login</a>
                @enderror
                {{-- <a href="{{ route('register', ['event' => $event->id]) }}">Belum Punya Akun</a>
                <button type="submit" class="btn btn-primary">Login</button> --}}
            </div>
            </form>
            </div>
        </div>
    </div>




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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
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

    @if ($errors->any())
        <script>
        $('#loginModal').modal('show');
        </script>
    @endif
</body>
</html>
