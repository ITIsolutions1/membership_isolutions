<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kota;
use App\Models\Event;
use App\Models\Anggota;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use App\Mail\DaftarDanRegister;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    // public function create(): View
    // {
    //     $domisili = Kota::pluck('nama_kota', 'id');        
    //     return view('auth.register', compact('domisili'));
    // }

    public function create(Request $request): View
{
    $domisili = Kota::pluck('nama_kota', 'id');

    // Ambil referral dari URL, contoh: /register?ref=000025
    $referral_code = $request->query('ref');

    return view('auth.register', compact('domisili', 'referral_code'));
}

    
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(Request $request): RedirectResponse
    // public function store(Request $request)
    // {
        
    //     $bioskops = explode(',', $request->bioskop);    
    //     // return $request->bioskop;
    //     // return 'berhasil';
    //     // Validasi input
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     // Membuat pengguna baru
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role' => 'member',
    //     ]);

    //     //UPLOAD GAMBAR 
    //     if($request->file('foto')){
    //         $fotoPath =  $request->file('foto')->store('member_image', 'public');
    //     } else {
    //         $fotoPath = null;
    //     }

    //     $anggota = $user->anggota()->create([
    //         'nama' => $request->nama_anggota,
    //         'about_me' => $request->about_me,
    //         'email' => $request->email,
    //         'nomor' => $request->nomor,
    //         'domisili' => $request->domisili,
    //         'tanggal_lahir' => $request->tanggal_lahir,
    //         'genre' => $request->genre,
    //         'foto' => $fotoPath
    //     ]);

    //     // $anggota->peminatan()->attach([1,2,3]);
    //     // 1 = nonton, 2 = seminar berbayar, 3 seminar gratis
    //     if(isset($request->nonton)){
    //         $anggota->peminatan()->attach(1);
    //         if(isset($request->bioskop)){
    //             foreach($bioskops as $bioskop){
    //                 $anggota->bioskop()->attach($bioskop);
    //             }
    //         }
    //     }
    //     if(isset($request->seminar)){
    //         $anggota->peminatan()->attach(3);
    //     }
    //     if(isset($request->traning_development)){
    //         $anggota->peminatan()->attach(2);
    //     }



        

    //     // Menyebarkan event registered
    //     event(new Registered($user));

    //     // Melakukan login otomatis
    //     Auth::login($user);
    //     redirect()->route('dashboard');
    //     // Cek role pengguna setelah login dan arahkan ke dashboard yang sesuai
    //     // if ($user->role === 'admin') {
    //     //     return redirect()->route('admin.dashboard');  // Pengguna admin
    //     // } elseif ($user->role === 'member') {
    //     //     return redirect()->route('member.dashboard');  // Pengguna member
    //     // }

    //     // Default redirect jika role tidak ditemukan
    //     return redirect(RouteServiceProvider::HOME); 
    // }

public function store(Request $request)
{
    $bioskops = explode(',', $request->bioskop);

    // Validasi input
    $request->validate([
        // 'name'           => ['required', 'string', 'max:255'],
        'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        'referral_code'  => ['nullable', 'string'],
    ]);

    // Buat user
    $user = User::create([
        // 'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'member',
    ]);

    // Upload Foto
    $fotoPath = $request->file('foto')
        ? $request->file('foto')->store('member_image', 'public')
        : null;

    // ============================
    // ⭐ PROSES REFERRAL
    // ============================
    $referrerAnggota = null;

    if ($request->referral_code) {

        // Convert "000025" → 25
        $referralId = intval($request->referral_code);

        // Cari anggota referrer
        $referrerAnggota = \App\Models\Anggota::find($referralId);

        if (!$referrerAnggota) {
            $referrerAnggota = null;
        }
    }

    // ============================
    // ⭐ BUAT ANGGOTA BARU
    // ============================
    $anggota = $user->anggota()->create([
        'nama'          => $request->nama_anggota,
        'about_me'      => $request->about_me,
        'email'         => $request->email,
        'nomor'         => $request->nomor,
        'domisili'      => $request->domisili,
        'tanggal_lahir' => $request->tanggal_lahir,
        'genre'         => $request->genre,
        'foto'          => $fotoPath,
        'referred_by'   => $referrerAnggota ? $referrerAnggota->id : null,
        'poin'          => 1,        // 👍 member baru dapat 1 poin
        'koin'          => 1000,     // 👍 member baru dapat 1000 koin
    ]);

    // ============================
    // ⭐ BONUS UNTUK PENGUNDANG
    // ============================
    if ($referrerAnggota) {
        $referrerAnggota->increment('poin', 1);     // pengundang dapat 1 poin
        $referrerAnggota->increment('koin', 1000);  // pengundang dapat 1000 koin
    }

    // ============================
    // ⭐ MINAT & BIOSKOP
    // ============================
    if ($request->nonton) {
        $anggota->peminatan()->attach(1);

        if ($request->bioskop) {
            foreach ($bioskops as $bioskop) {
                $anggota->bioskop()->attach($bioskop);
            }
        }
    }

    if ($request->seminar) {
        $anggota->peminatan()->attach(3);
    }

    if ($request->traning_development) {
        $anggota->peminatan()->attach(2);
    }

    // Event Register
    event(new Registered($user));

    // Auto login
    Auth::login($user);

    return redirect()->route('dashboard');
}




    public function create2(): View
    {
        $domisili = Kota::pluck('nama_kota', 'id');        
        return view('auth.register2', compact('domisili'));
    }

    // public function register2(Request $request){
    //     // return $request;
    //     $request->validate(
    //         [
    //             'name' => ['required', 'string', 'max:255'],
    //             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //         ],
    //         [
    //             'email.unique' => 'Email ini sudah terdaftar, silahkan login untuk mendaftar jika sudah punya akun',
    //             'email.email'    => 'Format email harus benar, contoh: nama@mail.com.',
    //         ]
    
    //     );
    //     $event = Event::findOrFail($request->event_id);
    //     // return $event;
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make('123456789'),
    //         'role' => 'member',
    //     ]);

    //      $anggota = $user->anggota()->create([
    //         'nama' => $request->name,            
    //         'email' => $request->email,
    //         'nomor' => $request->nomor,            
    //     ]);

    //     $anggota->eventsJoined()->syncWithoutDetaching([$request->event_id]);
    //     Mail::to($anggota->email)->queue(new daftarDanRegister($anggota, $event));
    //     return redirect()->back()->with('success', 'anda telah berhasil mendaftar event ini, silahkan cek email untuk informasi lebih lanjut');
    // }



    public function register2(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
    ]);

    $event = Event::findOrFail($request->event_id);

    // --- 1. Cari Referral ID jika ada ---
    $referralId = null;

    if ($request->referral_code) {
        // convert "000025" menjadi 25
        $referralId = intval($request->referral_code);

        // cek apakah anggota tersebut benar ada
        $referrer = User::find($referralId);

        if (!$referrer) {
            return back()->with('error', 'Kode referal tidak valid.');
        }
    }

    // --- 2. Buat User Baru ---
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make('123456789'),
        'role' => 'member',
    ]);

    // --- 3. Buat Data Anggota ---
    $anggota = $user->anggota()->create([
        'nama' => $request->name,
        'email' => $request->email,
        'nomor' => $request->nomor,
    ]);

    // --- 4. Insert ke pivot anggota_events + referal ---
    $anggota->eventsJoined()->attach($request->event_id, [
        'referred_by' => $referralId
    ]);

    // --- 5. Kirim Email ---
    Mail::to($anggota->email)->queue(new daftarDanRegister($anggota, $event));

    return redirect()->back()->with('success', 'Anda berhasil mendaftar event ini!');
}






    public function test2(){
        $event = Event::findOrFail(2);
        $anggota = Anggota::findOrFail(3);

        return view('emails.email_template.registerdandaftar', compact('event', 'anggota'));
    }

}
