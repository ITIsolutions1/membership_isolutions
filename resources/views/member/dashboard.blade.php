@extends('adminlte::page')

@section('title', 'Dashboard Member')

@section('content')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection



<style>
.referral-box {
    background: #ffffff;
    border-radius: 16px;
    padding: 30px 20px;
    margin: 25px auto;
    max-width: 450px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    border: 1px solid #f2f2f2;
}

.referral-icon {
    font-size: 40px;
    color: #d4a017; /* gold */
    margin-bottom: 10px;
}

.referral-title {
    font-weight: 700;
    font-size: 24px;
    color: #d4a017;
    margin-bottom: 8px;
}

.referral-subtitle {
    font-size: 14px;
    color: #6c757d;
    max-width: 500px;
    margin: auto;
    margin-bottom: 20px;
}

.referral-btn {
    background: #0069d9;
    color: white;
    border: none;
    padding: 12px 26px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    transition: 0.25s ease;
    box-shadow: 0 4px 10px rgba(0,123,255,0.3);
}

.referral-btn:hover {
    background: #0056b3;
    transform: translateY(-3px);
}
</style>


{{-- @dd($data) --}}
<div class="dashboard-container">
    <div class="row">
        {{-- Kiri: Kartu Member --}}
        <div class="col-md-5 col-left">
            <div class="member-card" style="background-image: url('{{ asset('images/depan.png') }}');">
                <div class="overlay">
                    <div style="position: absolute; 
                                top: 50%; 
                                left: 50%; 
                                transform: translate(-50%, -50%);
                                margin: 0;
                                width: 100%;
                                text-align: center;
                                pointer-events: none;">
                        
                        {{-- <h4>{{ str_pad($anggota->id, 4, '0', STR_PAD_LEFT) }}</h4> --}}
                        <h4 style="margin-bottom: 5px; font-size: 30px; color: #e7c47b; font-family: 'Poppins', sans-serif; font-weight: normal;">{{ $anggota->nama }}</h4>
                        <h4>{{ ucwords($anggota->level) }}</h4>
                        {{-- Nomor unik kartu --}}
                        <p style="font-size: 16px; color: #fff; font-weight: 400; margin: 0; color: #e7c47b;">
                        {{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                        </p>                
                    </div>
                    {{-- Kiri Bawah: Member Since --}}
                    <p style="font-family: 'Great Vibes', cursive; position: absolute; bottom: 30px; left: 15px; font-size: 16px; color: #e7c47b; font-weight: 400; margin: 0;">
                        Member Since: 
                        {{ $user->created_at->format('Y') }}
                    </p>                    
                </div>
            </div>
            <div class="member-card" style="background-image: url('{{ asset('images/depan.png') }}');">
                <div class="overlay">
                    <ul style="font-size: 10px; line-height: 1.5; margin-bottom: 20px; margin-top: 50px;">
                    <strong>Disclaimer Kartu Keanggotaan:</strong>         
                        <li>Kartu <strong>ini eksklusif dari komunitas membership ISolutions Indonesia</strong>.</li>
                        <li><strong>ISolutions Indonesia tidak bertanggung jawab</strong> atas kerugian atau kerusakan yang timbul akibat kehilangan atau penggunaan kartu yang tidak sah.</li>
                        <li>Kartu ini merupakan tanggung jawab pemilik, <strong>ISolutions Indonesia tidak bertanggung jawab atas penyalahgunaan</strong>.</li>
                        <li>Segala bentuk <strong>penyalahgunaan akan dikenakan sanksi</strong>, termasuk pembatalan akses tanpa kompensasi.</li>
                        <li><strong>Pelanggaran terhadap ketentuan ini</strong> dapat mengakibatkan pembatalan keikutsertaan secara mutlak.</li>
                        <li><strong>ISolutions berhak menolak akses</strong> apabila ditemukan penyalahgunaan.</li>
                        <li>Jika kartu ini ditemukan, mohon <strong>kembalikan ke ISolutions Indonesia</strong>.</li>
                    </ul>
                </div>
            </div>

                 <div class="referral-box text-center">

                    <div class="referral-icon">
                        <i class="fas fa-gift"></i>
                    </div>

                    <h3 class="referral-title">Ajak Teman & Dapatkan Koin!</h3>

                    <p class="referral-subtitle">
                        Bagikan link referral kamu dan kumpulkan lebih banyak koin setiap kali teman bergabung 🎉
                    </p>

                    <button class="referral-btn" onclick="copyReferral()">
                        <i class="fas fa-copy"></i> Copy Referral Link
                    </button>
                </div>
        </div>
   


        {{-- Kanan: Info Profil, Metrics, Ranking --}}
        <div class="col-md-7 col-right">        


            <div class="info-card"> 
                <div class="info-card">
                    <div class="d-flex align-items-center" id="foto_nama">                        
                        <div>
                            @if($anggota->foto)
                                <img style="width: 100px; height:100px; border-radius: 50%;" src="{{ asset('storage/'.$anggota->foto) }}" alt="Foto Profil">
                            @else
                                <img style="width: 100px; height:100px; border-radius: 50%;" src="{{ asset('images/no_profile.jpg') }}" alt="Foto Default">
                            @endif
                        </div>                        
                        <div class="ml-2 d-flex align-items-center">
                            <p style="font-size: 20px; font-weight: 600; margin: 0;">{{ $anggota->nama }}</p>
                            <i class="far fa-check-circle ml-2"></i>
                        </div>
                    </div>
                    <div id="about_me" class="mt-4 mb-4">
                        <p style="font-weight:500; color:rgb(115, 115, 115);">{{ $anggota->about_me }}</p>
                    </div>

                    <div id="data_diri">
                        <table>
                            <tr class="baris" >
                                <td style="width: 180px" class="title-data">Nomor Keanggotaan</td>
                                <td>:</td>
                                <td style="font-weight: 600"> {{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr class="baris">
                                <td class="title-data">Status Keanggotaan</td>
                                <td>:</td>
                                <td style="font-weight: 600">{{ ucwords($anggota->level) }}</td>
                                {{-- <td style="font-weight: 600"> {{ ucfirst($user->role) }}</td> --}}
                            </tr>
                            <tr class="baris">
                                <td class="title-data">Domisili</td>
                                <td>:</td>
                                <td style="font-weight: 600"> {{ $anggota->domisili }}</td>
                            </tr>
                            <tr class="baris">
                                <td class="title-data">Email</td>
                                <td>:</td>
                                <td style="font-weight: 600"> {{ $anggota->email }}</td>
                            </tr>
                            <tr class="baris">
                                <td class="title-data">Anggota Sejak</td>
                                <td>:</td>
                                <td style="font-weight: 600"> {{ $anggota->created_at->format('Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="pt-4 pb-4">
                        <a href="{{ route('member.profile') }}" style="width: 300px" class="btn btn-outline-primary">Lihat profil</a>
                    </div>
                </div>
                
            </div>

          <div class="metrics" style="margin-top: 35px;">
            <div class="metric-card">
                <i class="fas fa-trophy"></i>
                <div class="value">{{ $peringkat }}</div>
                <div class="label">Peringkat</div>
            </div>


                <div class="metric-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="value">{{ $poin }}</div>
                    <div class="label">Poin</div>
                </div>

                <div class="metric-card">
                    <i class="fas fa-coins"></i>
                    <div class="value">{{ $koin }}</div>
                    <div class="label">Koin</div>
                </div>
        </div>

        <div class="ranking-section" style="margin-top: 35px;">

            <h5>3 Peringkat Teratas Bulan Ini</h5>

            <div class="ranking-cards">

                @php
                    $labels = ['1st Place', '2nd Place', '3rd Place'];
                @endphp

                @for ($i = 0; $i < 3; $i++)
                    @php
                        $data = $monthlyTop3[$i] ?? null;

                        $name = $data->nama ?? '—';

                        $photo = $data && $data->foto
                            ? asset('storage/' . $data->foto)
                            : asset('images/no_profile.jpg');
                    @endphp

                    <div class="ranking-card">
                        <h6>{{ $labels[$i] }}</h6>

                        <div>
                            <img src="{{ $photo }}"
                                width="100"
                                height="100"
                                style="border-radius: 50%; object-fit: cover;">
                        </div>

                        <div class="place">{{ $name }}</div>

                        <!-- @if($data)
                            <div class="text-muted" style="font-size: 13px;">
                                {{ $data->total }} referral bulan ini
                            </div>
                        @endif -->
                    </div>

                @endfor

            </div>
        </div>






        </div>
    </div>






<!-- <div class="top-ref-section mt-5 pt-4"> 
@if($akses == 'admin')

    <h4 class="fw-bold text-danger text-center mb-5" style="font-size: 28px;">
        🏆 Top 5 Referrers
    </h4>

    <div class="row justify-content-center">
        @foreach($topReferrers->take(5) as $row)
            @php
                $referrer = \App\Models\Anggota::find($row->referred_by);
            @endphp

            <div class="col-md-4 mb-4">
                <div class="ref-card shadow-lg p-4 text-center position-relative">

                    <div class="rank-badge">
                        🏆{{ $loop->iteration }}
                    </div>

                    <h5 class="fw-bold text-dark mb-2" style="font-size: 22px;">
                        {{ $referrer ? $referrer->nama : 'Tidak ditemukan' }}
                    </h5>

                    <p class="text-secondary mb-1">
                        <strong>Kode Referal:</strong>
                        <span class="fw-bold text-danger ms-1">
                            {{ str_pad($row->referred_by, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </p>

                    <div class="invite-count mt-3 fw-bold" style="font-size: 20px;">
                        {{ $row->total }} Member
                    </div>

                </div>
            </div>
        @endforeach
    </div>

@endif
</div> -->



</div>
<script>
function copyReferral() {
    let referralCode = "{{ str_pad(Auth::user()->id, 6, '0', STR_PAD_LEFT) }}";
    let url = "{{ url('/register') }}" + "?ref=" + referralCode;

    navigator.clipboard.writeText(url).then(() => {
        alert("Referral link copied!\n" + url);
    });
}
</script>




<style>
.ref-card {
    background: white;
    border-radius: 18px;
    transition: 0.25s ease-in-out;
    transform-style: preserve-3d;
    cursor: pointer;
}

.ref-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.rank-badge {
    position: absolute;
    top: -12px;
    right: -12px;
    background: #ff0000;
    color: white;
    padding: 8px 14px;
    border-radius: 50px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(255, 0, 0, 0.4);
}

.invite-count {
    font-size: 1.2rem;
    background: #eef5ff;
    padding: 10px;
    border-radius: 10px;
    color: #ff0000;
    box-shadow: inset 0 0 8px rgba(13,110,253,0.15);
}
</style>

@endsection
