<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Httap\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $akses = Auth::user()->role;
    //     if($akses == 'admin'){   
    //         $kota = Auth::user()->anggota->domisili;                     
    //         $member = Anggota::get();   
    //         $user = auth()->user();
    //         $anggota = Anggota::where('user_id', $user->id)->first();
    //         $data = [$anggota, $member, $user];           
    //         return view('member.dashboard', compact(['member', 'user', 'data', 'kota', 'anggota', 'akses',]));

    //     } elseif($akses == 'koordinator') {
    //          $kota = Auth::user()->anggota->domisili;            
    //         $member = Anggota::with('user')->where('domisili', $kota)->where('level', 'member')->get();   
    //         $user = auth()->user();
    //         $anggota = Anggota::where('user_id', $user->id)->first();
    //         $genre = explode(',', $anggota->genre);            
    //         $data = [$anggota, $kota, $member, $user];           
    //         return view('member.dashboard', compact(['member', 'user', 'data', 'kota', 'anggota', 'akses', 'genre']));            

    //     } else{
    //         $kota = Auth::user()->anggota->domisili;            
    //         $user = auth()->user();
    //         $anggota = Anggota::where('user_id', $user->id)->first();
    //         $genre = explode(',', $anggota->genre);            
    //         $data = [$anggota, $kota, $user];        
    //         return view('member.dashboard', compact(['user', 'data', 'kota', 'anggota', 'akses', 'genre']));        
    //     }
        
    // }

//    public function index()
// {
//     $akses = Auth::user()->role;

//     // ======================
//     //  HITUNG TOP REFERRERS
//     //  HANYA JIKA ADMIN
//     // ======================
//     $topReferrers = null; // default untuk non-admin

//     if ($akses == 'admin') {
//      $topReferrers = DB::table('anggota_events')
//     ->select('referred_by', DB::raw('COUNT(*) as total'))
//     ->whereNotNull('referred_by')
//     ->groupBy('referred_by')
//     ->orderByDesc('total')
//     ->limit(5)  
//     ->get();

//     }

//     // =========================================
//     //                ADMIN
//     // =========================================
//     if($akses == 'admin'){   
//         $kota = Auth::user()->anggota->domisili;

//         $member = Anggota::get();   
//         $user = auth()->user();
//         $anggota = Anggota::where('user_id', $user->id)->first();

//         return view('member.dashboard', compact([
//             'member',
//             'user',
//             'kota',
//             'anggota',
//             'akses',
//             'topReferrers'  // hanya admin yang menerima
//         ]));
//     }

//     // =========================================
//     //              KOORDINATOR
//     // =========================================
//     elseif($akses == 'koordinator') {

//         $kota = Auth::user()->anggota->domisili;
//         $member = Anggota::with('user')
//             ->where('domisili', $kota)
//             ->where('level', 'member')
//             ->get();

//         $user = auth()->user();
//         $anggota = Anggota::where('user_id', $user->id)->first();
//         $genre = explode(',', $anggota->genre);

//         return view('member.dashboard', compact([
//             'member',
//             'user',
//             'kota',
//             'anggota',
//             'akses',
//             'genre',
//         ]));
//     }

//     // =========================================
//     //                MEMBER
//     // =========================================
//     else {

//         $kota = Auth::user()->anggota->domisili;
//         $user = auth()->user();
//         $anggota = Anggota::where('user_id', $user->id)->first();
//         $genre = explode(',', $anggota->genre);

//         return view('member.dashboard', compact([
//             'user',
//             'kota',
//             'anggota',
//             'akses',
//             'genre'
//         ]));
//     }
// }

public function index()
{
    $totalAnggota = Anggota::count();
    $user   = Auth::user();
    $akses  = $user->role;

    // Ambil data anggota dari user login
    $anggota = Anggota::where('user_id', $user->id)->firstOrFail();

    // ==============================
    //  TOP REFERRER (ALL TIME)
    // ==============================
    $topReferrers = null;

    if ($akses == 'admin') {
        $topReferrers = DB::table('anggotas')
            ->select('referred_by', DB::raw('COUNT(*) as total'))
            ->whereNotNull('referred_by')
            ->groupBy('referred_by')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $ref = \App\Models\Anggota::find($row->referred_by);
                $row->nama = $ref?->nama ?? 'Unknown';
                $row->foto = $ref?->foto ?? null;
                return $row;
            });
    }

    // ==============================
    //  Peringkat Global Berdasarkan Poin
    // ==============================
$peringkat = Anggota::select('id')
    ->selectRaw('(COALESCE(poin,0) + COALESCE(koin,0)) AS total')
    ->orderByDesc('total')
    ->orderBy('id') // tambahkan tie-breaker
    ->pluck('id')
    ->search($anggota->id) + 1;



    $poin = $anggota->poin;
    $koin = $anggota->koin;

    // ==============================
    //  TOP 3 REFERRER BULAN INI
    // ==============================
   $monthlyTop3 = DB::table('anggotas')
    ->select('referred_by', DB::raw('COUNT(*) as total'))
    ->whereNotNull('referred_by')
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->groupBy('referred_by')
    ->orderByDesc('total')
    ->take(3)
    ->get()
    ->map(function ($row) {
        $anggota = \App\Models\Anggota::find($row->referred_by);
        return (object) [
            'id'     => $row->referred_by,
            'total'  => $row->total,
            'nama'   => $anggota?->nama ?? 'Unknown',
            'foto'   => $anggota?->foto ?? null,
            'poin'   => $anggota?->poin ?? 0,
            'koin'   => $anggota?->koin ?? 0,
        ];
    });


    // ======================================
    //                ADMIN
    // ======================================
    if ($akses == 'admin') {

        $kota   = $anggota->domisili;
        $member = Anggota::all();

        return view('member.dashboard', compact(
            'member',
            'user',
            'kota',
            'anggota',
            'akses',
            'topReferrers',
            'peringkat',
            'poin',
            'koin',
            'monthlyTop3'
        ));
    }

    // ======================================
    //             KOORDINATOR
    // ======================================
    elseif ($akses == 'koordinator') {

        $kota   = $anggota->domisili;
        $member = Anggota::with('user')
            ->where('domisili', $kota)
            ->where('level', 'member')
            ->get();

        $genre = explode(',', $anggota->genre);

        return view('member.dashboard', compact(
            'member',
            'user',
            'kota',
            'anggota',
            'akses',
            'genre',
            'peringkat',
            'poin',
            'koin',
            'monthlyTop3'
        ));
    }

    // ======================================
    //                MEMBER
    // ======================================
    else {

        $kota  = $anggota->domisili;
        $genre = explode(',', $anggota->genre);

        return view('member.dashboard', compact(
            'user',
            'kota',
            'anggota',
            'akses',
            'genre',
            'peringkat',
            'poin',
            'koin',
            'monthlyTop3'
        ));
    }
}


    

    public function memberDashboard()
{
    $user = auth()->user();

    return view('member.dashboard', [
        'user' => $user,
        'memberId' => '8630 - 082', // Gantilah sesuai logika ID kamu
        'ranking' => 696,
        'points' => 30,
        'coins' => 30,
    ]);
}

}

