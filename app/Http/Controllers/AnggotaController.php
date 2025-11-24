<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Kota;
use App\Models\Anggota;
use App\Models\Bioskop;
use App\Models\Peminatan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    //
    ////////////////////////
    //UNTUK NAMPILIN LIST
    ////////////////////////    
    // public function index(){
        
    //     if(Auth::user()->role == 'admin'){
    //         $member = Anggota::paginate(10);
    //         return view('admin.memberlist', compact(['member']));
    //     } else {
    //         $kota = Auth::user()->anggota->kota->first()->nama_kota;
    //         // $member = Anggota::with('user')->where('domisili', $kota)->where('level', 'member')->get();   
    //         $member = Anggota::all();
    //         $user = auth()->user();
    //     }     
    //     // return $member;
    //     return view('admin.memberlist', compact(['member', 'kota', 'user']));
    // }
    // public function index(Request $request)
    //     {
            
    //         $query = Anggota::with('user');

    //         // Search
    //         if ($request->has('search') && $request->search != '') {
    //             $search = $request->search;
    //             $query->where(function ($q) use ($search) {
    //                 $q->where('nama', 'like', "%{$search}%")
    //                 ->orWhere('domisili', 'like', "%{$search}%")
    //                 ->orWhereHas('user', function ($q2) use ($search) {
    //                     $q2->where('email', 'like', "%{$search}%");
    //                 });
    //             });
    //         }

    //         // Per page pagination
    //         $perPage = $request->get('per_page', 10);
    //         if ($perPage === 'all') {
    //             $member = $query->orderBy('created_at', 'desc')->get(); // all data
    //         } else {
    //             $member = $query->orderBy('created_at', 'desc')->paginate((int)$perPage)->appends($request->all());
    //         }

    //         return view('admin.memberlist', compact('member'));
    //     }


   public function index(Request $request)
    {
        $query = Anggota::with('user', 'peminatan');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('domisili', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('email', 'like', "%{$search}%");
                });
            });
        }

        // Filter by peminatan
        if ($request->filled('peminatan_id')) {
            $peminatan = $request->peminatan_id;
            $query->whereHas('peminatan', function ($q) use ($peminatan) {
                $q->where('peminatan', $peminatan);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['nama', 'created_at', 'domisili', 'level', 'email'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        if ($sort === 'email') {
            $query->join('users', 'anggotas.user_id', '=', 'users.id')
                ->orderBy('users.email', $direction)
                ->select('anggotas.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        if ($perPage === 'all') {
            $member = $query->get();
        } else {
            $member = $query->paginate((int)$perPage)->appends($request->all());
        }

        return view('admin.memberlist', compact('member'));
    }




    ////////////////////////
    //UNTUK NAMPILIN FORM PROFILE
    ////////////////////////
    public function profile(){
        $anggota = Auth::user()->anggota;
        $peminatan = $anggota->peminatan->pluck('id')->toArray();        
        $kotas = Kota::all();        
        return view('member.profile', compact(['anggota', 'kotas', 'peminatan']));
    }

    ///////////////////////////////
    //UNTUK MENGIRIMKAN PERUBAHAN PROFILE
    ///////////////////////////////
    public function profile_update(Request $request){
       try {            
            $userid = Auth::user()->id;
            $user = User::where('id', $userid)->first();            
            $peminatan = [];
            if($request->nonton){
                array_push($peminatan, 1);
            }
            if($request->training_development){
                array_push($peminatan, 2);
            }   
            if($request->seminar){
                array_push($peminatan, 3);
            }
            
        
            
            // Update nama user
            $user->update([
                'name' => $request->nama,
            ]);    

            // Update data anggota
            $anggota = $user->anggota;        
            $anggota->update([  
                'nama' => $request->nama,
                'about_me' => $request->about_me,
                'tanggal_lahir' => $request->tanggal_lahir,
                'email' => $request->email,
                'nomor' => $request->nomor,
                'sponsor' => $request->sponsor,
                'genre' => $request->genre,
                'domisili' => $request->domisili,   
            ]);

            //update peminatan 
            if(empty($peminatan)){
                $anggota->peminatan()->detach();
            }
            $anggota->peminatan()->sync($peminatan);

            // ================================
            // ⬇️ Tambahkan untuk update pivot ⬇️
            // ================================
            if(is_null($request->bioskop)){
                $anggota->bioskop()->detach();
            }
            if ($request->filled('bioskop')) {
                $bioskopIds = explode(',', $request->bioskop); // misal: ['26', '27', '28']
                $anggota->bioskop()->sync($bioskopIds); // Ini akan update pivot table
            }
            //==============================
            // SIMPAN PERUBAHAN GAMBAR
            //==============================

             if ($request->hasFile('foto')) {
                // Hapus gambar lama jika ada
                if ($anggota->foto && Storage::disk('public')->exists($anggota->foto)) {
                    Storage::disk('public')->delete($anggota->foto);
                }

                // Simpan gambar baru
                $path = $request->file('foto')->store('member_image', 'public');

                // Update kolom gambar
                $anggota->update(['foto' => $path]);
            }



                if($request->password){
                    $user->update([
                         'password' => Hash::make($request->password)
                    ]);
                    return redirect()->route('member.profile')->with('success', 'Profil dan Password berhasil diperbarui!');
                }else {
                    return redirect()->route('member.profile')->with('success', 'Profil berhasil diperbarui!');
                }
                
            } catch(\Exception) {             
                return redirect()->route('member.profile')->with('error', 'Gagal memperbarui data');
            }
    }

    ////////////////////////
    //UNTUK NAMPILIN FORM CREATE MEMBER
    ////////////////////////
    public function create(){
        $domisili = Kota::pluck('nama_kota', 'id');        
        return view('admin.create_member', compact('domisili'));
    }

    ////////////////////////
    //UNTUK BUAT MEMBER BARU CREATE MEMBER
    ////////////////////////
    public function store(Request $request){              
        try{            
            $bioskops = explode(',', $request->bioskop);    
            
            $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email',
                    'password' => 'required|confirmed',
                    'nama_anggota' => 'required|string|max:255',
                    'domisili' => 'required|string',
                    'nomor' => 'required|string|max:20',
                    'tanggal_lahir' => 'required|date',
                ]);

                // Membuat pengguna baru
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);

                //UPLOAD GAMBAR 
                if($request->file('foto')){
                  $fotoPath =  $request->file('foto')->store('member_image', 'public');
                } else {
                    $fotoPath = null;
                }
                $anggota = $user->anggota()->create([
                    'nama' => $request->nama_anggota,
                    'about_me' => $request->about_me,
                    'email' => $request->email,
                    'nomor' => $request->nomor,
                    'domisili' => $request->domisili,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'genre' => $request->genre,
                    'level' => $request->role == 'admin' ? 'super admin' : $request->role,
                    'akses_level' => $request->role == 'admin' ? 'super admin' : $request->role,
                    'foto' => $fotoPath
                ]);

                // $anggota->peminatan()->attach([1,2,3]);
                // 1 = nonton, 2 = seminar berbayar, 3 seminar gratis
                if(isset($request->nonton)){
                    $anggota->peminatan()->attach(1);
                    if(isset($request->bioskop)){
                        foreach($bioskops as $bioskop){
                            $anggota->bioskop()->attach($bioskop);
                        }
                    }
                }
                if(isset($request->seminar)){
                    $anggota->peminatan()->attach(3);
                }
                if(isset($request->training_development)){
                    $anggota->peminatan()->attach(2);
                }

                return redirect()->route('memberlist')->with('success_create', 'menambahkan member baru');
                    
        } catch(\Exception) {
                return redirect()->route('memberlist')->with('error_create', 'menambahkan member');
        }

    }

    ////////////////////////
    //UNTUK NAMPILIN FORM EDIT MEMBER
    ////////////////////////
    public function edit($id_user){
        
        $user = User::with('anggota')->where('id', $id_user)->first();
        $anggota =  $user->anggota;
        $peminatan = $anggota->peminatan->pluck('id')->toArray();
        $bioskop = $anggota->bioskop;
        $domisili = Kota::pluck('nama_kota', 'id');   
                
        return view('admin.edit_member', compact(['user', 'anggota', 'peminatan', 'bioskop', 'domisili']));
    }
    
    ////////////////////////
    //UNTUK SIMPAN PERUBAHAN EDIT MEMBER
    ////////////////////////
    public function update(Request $request){
        
        try {

            $userid = $request->user_id;
            $user = User::where('id', $userid)->first();

            $peminatan = [];
            if($request->nonton){
                array_push($peminatan, 1);
            }
            if($request->seminar){
                array_push($peminatan, 2);
            }
            if($request->training_development){
                array_push($peminatan, 3);
            }   
        
            
            // Update nama user
            $user->update([
                'name' => $request->name,
                'role' => $request->role
            ]);    

            // Update data anggota
            $anggota = $user->anggota;        
            $anggota->update([  
                'nama' => $request->name,
                'about_me' => $request->about_me,
                'tanggal_lahir' => $request->tanggal_lahir,
                'email' => $request->email, 
                'nomor' => $request->nomor,
                'sponsor' => $request->sponsor,
                'genre' => $request->genre,
                'level' => $request->role == 'admin' ? 'super admin' : $request->role,
                'akses_level' => $request->role == 'admin' ? 'super admin' : $request->role,
                'domisili' => $request->domisili,       
            ]);

            //update peminatan 
            if(empty($peminatan)){
                $anggota->peminatan()->detach();
            }
        $anggota->peminatan()->sync($peminatan);

            // ================================
            // ⬇️ Tambahkan untuk update pivot ⬇️
            // ================================
            if(is_null($request->bioskop)){
                $anggota->bioskop()->detach();
            }
            if ($request->filled('bioskop')) {
                $bioskopIds = explode(',', $request->bioskop); // misal: ['26', '27', '28']
                $anggota->bioskop()->sync($bioskopIds); // Ini akan update pivot table
            }

            
            if ($request->hasFile('foto')) {
                // Hapus gambar lama jika ada
                if ($anggota->foto && Storage::disk('public')->exists($anggota->foto)) {
                    Storage::disk('public')->delete($anggota->foto);
                }

                // Simpan gambar baru
                $path = $request->file('foto')->store('member_image', 'public');

                // Update kolom gambar
                $anggota->update(['foto' => $path]);
            }

            return redirect()->route('memberlist')->with('success', 'data member berhasil diperbarui!');
        } catch(\Exception) {             
            return redirect()->route('memberlist')->with('error', 'Terjadi kesalahan saat memperbarui data!');
        }
}

    ////////////////////////
    //UNTUK MENGHAPUS DATA MEMBER
    ////////////////////////
    public function delete($id_user){        
    
        try{
            $user = User::where('id', $id_user)->first();
            $anggota = $user->anggota;            
            // if(!empty($anggota->eventsCreated)){
                
            // }
            if (!$user) {
                    return redirect()->back()->with('error', 'User tidak ditemukan');
                }
                
                if ($anggota) {
                    $anggota->peminatan()->detach();
                    $anggota->bioskop()->detach();
                    $anggota->delete();
                }

                $user->delete();

                return redirect()->route('memberlist')->with('success', ' menghapus data member');
            } catch(\Exception){
                return redirect()->route('memberlist')->with('error', 'Terjadi kesalahan saat menghapus');
            }
    }

    ////////////////////////
    //UNTUK MENDAPATKAN GENRE ANGGOTA TERTENTU BUAT KEBUTUHAN JAVASCRIPT
    ////////////////////////
    public function get_genre($id_anggota){
        $genre = Anggota::where('id', $id_anggota)->first()->genre;
        $array_genre = explode(',', $genre);        
        return response()->json($array_genre);
    }

    ////////////////////////
    //UNTUK MENDAPATKAN BIOSKOP ANGGOTA TERTENTU BUAT KEBUTUHAN JAVASCRIPT
    ////////////////////////
    public function get_bioskop($id_anggota){
        $anggota = Anggota::where('id', $id_anggota)->first();
        $bioskop = $anggota->bioskop;
        return $bioskop;
    }
       
}
