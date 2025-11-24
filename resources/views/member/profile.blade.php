{{-- filepath: resources/views/member/profile.blade.php --}}
@extends('adminlte::page')

@section('title', 'Profil Anggota')

@section('content_header')
    {{-- <h1 class="mb-4">Profil Saya</h1> --}}
@endsection

@section('content')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection
<div class="profile-bg">
    @if (session('success'))
        <div id="popup-message" class="alert alert-success alert-dismissible fade show my-4 shadow-sm" role="alert" style="font-size: 1rem; animation: slideIn 0.5s ease-out;">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif



    <div class="container-fluid px-0">
        <div class="profile-title text-center mt-4 mb-5">
            <p style="font-size: 30px" id="profile-title">Profile</p>
        </div>
        <form method="POST" action="{{ route('member.profile.update') }}" class="profile-form bg-white rounded-0 shadow-none px-0 px-md-5 py-4 mx-auto" style="max-width:900px;" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <label for="nama" class="profile-form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $anggota->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="profile-form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $anggota->tanggal_lahir) }}" required>
                </div>
            </div>
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <label for="email" class="profile-form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $anggota->email) }}" {{ Auth::user()->role != 'admin' ? 'readonly' : ''  }}>
                </div>
                <div class="col-md-6">
                    <label for="nomor" class="profile-form-label">Nomor HP</label>
                    <input type="text" class="form-control" id="nomor" name="nomor" value="{{ old('nomor', $anggota->nomor) }}">
                </div>
            </div>
            {{-- <div class="mb-3">
                <label for="genre" class="profile-form-label">Genre Favorit</label>
                <input type="text" class="form-control" id="genre" name="genre" value="{{ old('genre', $anggota->genre) }}" placeholder="Pisahkan dengan koma, contoh: horor,action,comedy">
            </div> --}}
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <label for="domisili" class="profile-form-label">Domisili</label>
                    <select class="form-control" id="domisili-select" data-domisili="{{ $anggota->domisili }}" name="domisili" required>
                        <option value="" disabled {{ old('domisili', $anggota->domisili) ? '' : 'selected' }}>Pilih domisili</option>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota->nama_kota }}"
                                {{ old('domisili', $anggota->domisili) == $kota->nama_kota ? 'selected' : '' }}>
                                {{ $kota->nama_kota }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="level" class="profile-form-label">Level</label>
                    <input type="text" class="form-control" id="level" name="level" value="{{ old('level', $anggota->level) }}" {{ Auth::user()->role != 'admin' ? 'readonly' : ''  }}>
                </div>
                <div class="col-md-3">
                    <label for="akses_level" class="profile-form-label">Akses Level</label>
                    <input type="text" class="form-control" id="akses_level" name="akses_level" value="{{ old('akses_level', $anggota->akses_level) }}" {{ Auth::user()->role != 'admin' ? 'readonly' : ''  }}>
                </div>
            </div>

             <div class="col-md-6">
                    <label for="sponsor" class="profile-form-label">Sponsor</label>
                    <input type="sponsor" class="form-control" id="sponsor" name="sponsor" value="{{ old('sponsor', $anggota->sponsor) }}">
            </div>

            <hr>
            <div class="col-md-12">
                <label for="about_me" class="profile-form-label">About Me</label>                
                <textarea name="about_me" id="about_me" class="form-control" rows="3" >{{ old('about_me', $anggota->about_me) }}</textarea>
            </div>
            <hr>

            <div class="form-group">
                <label for="gambar">Foto Profil</label>
                <input type="file" class="form-control-file" id="gambar" name="foto" accept="image/*" onchange="previewImage(event)">

                <div id="preview-container" class="mt-3 d-none">
                    <p class="mb-2 font-weight-bold">Preview Foto baru:</p>
                    <img id="preview" class="img-thumbnail rounded border" style="max-height: 250px; object-fit: cover;">
                </div>

                {{-- <div id="preview-container2" class="mt-3">
                    @if($anggota->foto)
                        <p class="mb-2 font-weight-bold">Foto Saat Ini:</p>
                        <img src="{{ asset('storage/' . $anggota->foto) }}" class="img-thumbnail rounded border" style="max-height: 250px; object-fit: cover;">
                    @else
                        <h2>Tidak ada Foto Profil</h2>
                    @endif
                </div> --}}

                @if($anggota->foto)
                    <div id="preview-container2" class="mt-3">
                        <p class="mb-2 font-weight-bold">Foto Saat Ini:</p>
                        <img src="{{ asset('storage/' . $anggota->foto) }}" class="img-thumbnail rounded border" style="max-height: 250px; object-fit: cover;">
                    </div>
                @endif
            </div>

            <hr>

            <div class="form-group">
                <label>Peminatan</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="nonton" id="nonton" {{ in_array(1, $peminatan) ? 'checked' : '' }}>
                    <label class="form-check-label">Nonton</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="seminar" {{ in_array(3, $peminatan) ? 'checked' : '' }}>
                    <label class="form-check-label">Seminar</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="training_development" {{ in_array(2, $peminatan) ? 'checked' : '' }}>
                    <label class="form-check-label">Training Development</label>
                </div>
            </div>
            <div class="container"  id="genre_bioskop" data-anggota="{{ $anggota->id }}" data-get_bioskop="{{ route('get_bioskop', $anggota->id) }}" data-get_genre="{{ route('get_genre', $anggota->id) }}">
                <div class="row">

                    <div class="col-sm ">
                        <div id="bioskop-container">
                            <label for="bioskop">Bioskop</label>
                            <select id="bioskop-select" name="bioskop" class="form-control">
                                <option value="" disabled selected>Pilih bioskop</option>
                            </select>
                            <div id="bioskop-tags" class="flex flex-wrap gap-2 mt-2">

                            </div>
                            <!-- Hidden input untuk submit array genre -->
                            <input type="hidden" name="bioskop" id="bioskop-hidden">
                        </div> 
                    </div>

                    <div class="ms-5 col-sm" id="genre-container">
                        <label for="genre">Genre Favorit</label>
                        <select id="genre-select" class="form-control">
                            <option value="" disabled selected>Pilih genre</option>
                            <option value="Action">Action</option>
                            <option value="Drama">Drama</option>
                            <option value="Comedy">Comedy</option>
                            <option value="Thriller">Thriller</option>
                            <option value="Horror">Horror</option>
                        </select>
                        <div id="genre-tags" class="flex flex-wrap gap-2 mt-2"></div>
                        <!-- Hidden input untuk submit array genre -->
                        <input type="hidden" name="genre" id="genre-hidden">
                    </div>

                    
                    
                </div>  
            </div>  
            <hr class="my-4">
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <label for="password" class="profile-form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="profile-form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success px-5 py-2 rounded-pill">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>

    ///////////////
    //PREVIEW FOTO PROFIL
    //////////////////////////////

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const container = document.getElementById('preview-container');
        const container2 = document.getElementById('preview-container2');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            if (container2) {
                container2.classList.add('d-none');
            }            
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }


$(document).ready(function () {
    const anggotaId = $('#genre_bioskop').data('anggota');
    const link_api_genre = $('#genre_bioskop').data('get_genre');
    const link_api_bioskop = $('#genre_bioskop').data('get_bioskop');

    let selectedGenres = [];
    let selectedBioskop = [];
    let selectedBioskop_name = [];

    // Load genre sebelumnya
    $.get(link_api_genre, function(data) {
        data.forEach(element => {
            selectedGenres.push(element);
            $('#genre-tags').append(`
                <span class="badge badge-pill badge-secondary d-inline-flex align-items-center">
                    ${element}
                    <button type="button" class="btn p-0 ml-2 text-white border-0 bg-transparent" data-val="${element}">&times;</button>
                </span>
            `);
        });
        updateHiddenInput1();
    });

    // Load bioskop sebelumnya
    $.get(link_api_bioskop, function(data){
        data.forEach(element => {
            selectedBioskop.push(`${element.id}`);
            selectedBioskop_name.push(element);
        });

        selectedBioskop_name.forEach(element => {
            $('#bioskop-tags').append(`
                <span class="badge badge-pill badge-secondary d-inline-flex align-items-center">
                    ${element.bioskop}
                    <button type="button" class="btn p-0 ml-2 text-white border-0 bg-transparent" data-val="${element.id}">&times;</button>
                </span>
            `);
        });

        updateHiddenInput2();
    });

    // Toggle tampilan awal peminatan
    togglePeminatan();

    $('#nonton').on('change', togglePeminatan);

    function togglePeminatan() {
        if ($('#nonton').is(':checked')) {
            $('#bioskop-container').show();
            $('#genre-container').show();
        } else {
            $('#bioskop-container').hide();
            $('#genre-container').hide();
        }
    }

    // Tambah genre
    $('#genre-select').on('change', function () {
        const val = $(this).val();
        const text = $(this).find('option:selected').text();

        if (val && !selectedGenres.includes(val)) {
            selectedGenres.push(val);
            $('#genre-tags').append(`
                <span class="badge badge-pill badge-secondary d-inline-flex align-items-center">
                    ${text}
                    <button type="button" class="btn p-0 ml-2 text-white border-0 bg-transparent" data-val="${val}">&times;</button>
                </span>
            `);
            updateHiddenInput1();
        }

        $(this).val('');
    });

    $('#genre-tags').on('click', 'button', function () {
        const val = $(this).data('val');
        selectedGenres = selectedGenres.filter(v => v != val);
        $(this).parent().remove();
        updateHiddenInput1();
    });

    function updateHiddenInput1() {
        $('#genre-hidden').val(selectedGenres.join(','));
    }

    // Tambah bioskop
    $('#bioskop-select').on('change', function () {
        const val = $(this).val();
        const text = $(this).find('option:selected').text();

        if (val && !selectedBioskop.includes(val) && selectedBioskop.length < 3) {
            selectedBioskop.push(val);
            $('#bioskop-tags').append(`
                <span class="badge badge-pill badge-secondary d-inline-flex align-items-center">
                    ${text}
                    <button type="button" class="btn p-0 ml-2 text-white border-0 bg-transparent" data-val="${val}">&times;</button>
                </span>
            `);
            updateHiddenInput2();
        }

        $(this).val('');
    });

    $('#bioskop-tags').on('click', 'button', function () {
        const val = $(this).data('val');
        selectedBioskop = selectedBioskop.filter(v => v != val);
        $(this).parent().remove();
        updateHiddenInput2();
    });

    function updateHiddenInput2() {
        $('#bioskop-hidden').val(selectedBioskop.join(','));
    }

    // Ganti domisili → load bioskop
    domisili = $('#domisili-select').data('domisili');
    $.get(`/api/bioskop/search/${domisili}`, function(data) {
            $('#bioskop-select').empty();

            if (data.length === 0) {
                $('#bioskop-select').append(`<option>Tidak ada Bioskop</option>`);
            } else {
                $('#bioskop-select').append(`<option value="" disabled selected>Pilih bioskop</option>`);
                data.forEach(function(item) {
                    $('#bioskop-select').append(`<option value="${item.id}">${item.bioskop}</option>`);
                });
            }
        });
    
    $('#domisili-select').on('change', function() {
        const wilayah = $(this).val();

        $.get(`/api/bioskop/search/${wilayah}`, function(data) {
            $('#bioskop-select').empty();

            if (data.length === 0) {
                $('#bioskop-select').append(`<option>Tidak ada Bioskop</option>`);
            } else {
                $('#bioskop-select').append(`<option value="" disabled selected>Pilih bioskop</option>`);
                data.forEach(function(item) {
                    $('#bioskop-select').append(`<option value="${item.id}">${item.bioskop}</option>`);
                });
            }
        });
    });
});
</script>
@endpush

@endsection