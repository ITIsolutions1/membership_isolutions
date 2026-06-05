@extends('adminlte::page')

@section('title', 'Event List')

@section('content_header')
    <h1 class="mb-4">Event List</h1>
@endsection

@section('content')
@php
    $currentSort = request()->get('sort', 'created_at');
    $currentDirection = request()->get('direction', 'desc');
@endphp

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

    <!-- Header + Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="m-3">
            <form method="GET" class="form-inline mb-3">
                <select name="category" class="form-control mr-2">
                    <option value="">Semua Kategori</option>
                    <option value="nonton" {{ request('category') == 'nonton' ? 'selected' : '' }}>Nonton</option>
                    <option value="seminar" {{ request('category') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                    <option value="training development" {{ request('category') == 'training development' ? 'selected' : '' }}>Training Development</option>
                </select>
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama event" value="{{ request('search') }}">

                <select name="per_page" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                </select>

                <button type="submit" class="btn btn-primary">Terapkan</button>
            </form>
        </div>
        <a href="{{ route('events.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Add New Event
        </a>
    </div>

    <!-- Event Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Poster</th>
                        <th scope="col">
                            Event Name
                            <span>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => 'asc', 'page' => 1]) }}"
                                    style="text-decoration: none; {{ $currentSort == 'nama' && $currentDirection == 'asc' ? 'font-weight: bold;' : '' }}">
                                        &#9650;
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => 'desc', 'page' => 1]) }}"
                                    style="text-decoration: none; {{ $currentSort == 'nama' && $currentDirection == 'desc' ? 'font-weight: bold;' : '' }}">
                                        &#9660;
                                </a>
                            </span>
                        </th>
                        {{-- <th scope="col">Date</th> --}}
                         <th scope="col">
                            <span>Date</span>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal', 'direction' => 'asc']) }}"
                                style="text-decoration: none; {{ $currentSort == 'tanggal' && $currentDirection == 'asc' ? 'font-weight: bold;' : '' }}">
                                &#9650;
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal', 'direction' => 'desc']) }}"
                                style="text-decoration: none; {{ $currentSort == 'tanggal' && $currentDirection == 'desc' ? 'font-weight: bold;' : '' }}">
                                &#9660;
                            </a>
                        </th>
                        <th scope="col">Time</th>
                        <th scope="col">Category</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $index => $event)
                        <tr>
                            <td style="cursor:pointer;" onclick="window.location='{{ route('events.show', $event->id) }}'">{{ $index + 1 }}</td>
                            <td style="cursor:pointer;" onclick="window.location='{{ route('events.show', $event->id) }}'">
                                <img src="{{ asset('storage/' . $event->gambar) }}" alt="poster" class="img-thumbnail" style="height: 60px; width: 60px; object-fit: cover;">
                            </td>
                            <td style="cursor:pointer;" onclick="window.location='{{ route('events.show', $event->id) }}'">{{ $event->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->tanggal)->format('d M Y') }}</td>
                            <td>{{ $event->waktu}}</td>
                            {{-- <td>{{ \Carbon\Carbon::parse($event->waktu)->format('H:i') }}</td> --}}
                            <td><span class="badge badge-info">{{ ucwords($event->jenis_peminatan) }}</span></td>
                            <td>
                               <div class="dropdown dropleft">
                                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                                        Option
                                    </a>

                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('events.delete', $event->id) }}" onclick=" return confirm('Apakah Anda Yakin Mau Menghapus Event Ini ? ')">Delete</a>
                                        <a class="dropdown-item" href="{{ route('events.edit', $event->id) }}">Edit</a>
                                        <a class="dropdown-item" id="kirim_notifikasi" href="{{ route('emails.notification', $event->id) }}">Send Notification to Participant</a>
                                        <a class="dropdown-item" id="broadcast" href="{{ route('broadcast.email', $event->id) }}">Broadcast Email</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($events->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No events have been created.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-3 pb-3 d-flex justify-content-end">
            @if(method_exists($events, 'links'))
                {{ $events->links() }}
            @endif
        </div>
    </div>
    <div class="modal fade" id="notifModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="notifForm" method="GET">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Kirim Notifikasi
                    </h5>
                </div>

                <div class="modal-body">

                    <label>Pesan Tambahan</label>

                    <textarea
                        name="pesan"
                        class="form-control"
                        rows="5"
                        placeholder="Tambahkan pesan..."
                    ></textarea>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Kirim Email
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

    <!-- Modal Loading -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-sm text-center p-5">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <h5 class="mb-0">Mengirim notifikasi email...</h5>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    $('#kirim_notifikasi').on('click', function (event) {

        event.preventDefault();

        let url = $(this).attr('href');

        $('#notifForm').attr('action', url);

        $('#notifModal').modal('show');
    });
</script>

@endpush
