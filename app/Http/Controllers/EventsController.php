<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;

use function PHPUnit\Framework\returnSelf;

class EventsController extends Controller
{
    //
    public function index(){
        $events = Event::orderBy('created_at', 'desc')->paginate(10);
        return view('events.event', compact('events'));
    }

   public function index_admin(Request $request)
{
    $query = Event::query();

    // Filter: Search
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%");
        });
    }

    // Filter: Category
    if ($request->has('category') && $request->category != '') {
        $query->where('jenis_peminatan', $request->category);
    }

    // Sorting
    $sortField = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');

    // Whitelist untuk mencegah kolom tidak valid
    $allowedSorts = ['nama', 'tanggal', 'created_at'];
    $allowedDirections = ['asc', 'desc'];

    if (!in_array($sortField, $allowedSorts)) {
        $sortField = 'created_at';
    }
    if (!in_array($sortDirection, $allowedDirections)) {
        $sortDirection = 'desc';
    }

    $query->orderBy($sortField, $sortDirection);

    // Pagination
    $perPage = $request->get('per_page', 10);
    if ($perPage === 'all') {
        $events = $query->get(); // Ambil semua tanpa paginate
    } else {
        $events = $query->paginate((int)$perPage)->appends($request->all());
    }

    return view('events.event_list', compact('events'));
}

    
    // public function index_admin(Request $request){
    //      $query = Event::query();
    //         // Search
    //         if ($request->has('search') && $request->search != '') {
    //             $search = $request->search;
    //             $query->where(function ($q) use ($search) {
    //                 $q->where('nama', 'like', "%{$search}%");
    //             });
    //         }
    //         // Per page pagination
    //         $perPage = $request->get('per_page', 10);
    //         if ($perPage === 'all') {
    //             $events = $query->orderBy('created_at', 'desc')->get(); // all data
    //         } else {
    //             $events = $query->orderBy('created_at', 'desc')->paginate((int)$perPage)->appends($request->all());
    //         }
    //         return view('events.event_list', compact('events'));
    // }


    public function show($id){
       try{
        $event = Event::findOrFail($id);
        $anggota = Auth::user()->anggota;
        $peserta = $event->anggotaJoined;
        $terdaftar = true;

        if($anggota->eventsJoined()->where('events_id', $id)->exists()){
            $terdaftar = true;
        } else {
            $terdaftar = false;
        }             
        if(Auth::user()->role == 'admin'){
            return view('events.show', compact('event', 'terdaftar', 'peserta'));
        }else{
            return view('events.show', compact('event', 'terdaftar'));
        }
       }catch(\Exception){
            return redirect()->back()->with('error', 'terjadi kesalahan tidak dapat menampilkan');
       }
                       
    }
    public function show2($id){
        $event = Event::findOrFail($id);
        return view('events.show2', compact('event'));
    }   

    public function create()
    {
        return view('events.create');
    }


    public function store(Request $request)
    {
         $request->validate([
            'nama' => 'string|max:255',
            'deskripsi' => 'string',
            'gambar' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if($request->gambar){
                $gambarPath = $request->file('gambar')->store('event_images', 'public');
            }else{
                $gambarPath = NULL;
            }
            

            Event::create([
                'anggota_id' => Auth::user()->anggota->id,
                'createdBy' => Auth::user()->name,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'narasumber' => $request->narasumber,
                'jenis_peminatan' => $request->jenis_peminatan,
                'Lokasi' => $request->Lokasi,
                'link' => $request->link,
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'wilayah_koordinator' => $request->wilayah_koordinator,
                'gambar' => $gambarPath,
            ]);

            return redirect()->route('events.index.admin')->with('success', 'Event berhasil dibuat!');
        try{

           
        }catch(\Exception){
             return redirect()->route('events.index.admin')->with('error', 'Terjadi Kesalahan');
        }
    }

    public function delete($id){
        try{
            $event = Event::findOrFail($id);

            // Hapus gambar jika ada
            if ($event->gambar && Storage::disk('public')->exists($event->gambar)) {
                Storage::disk('public')->delete($event->gambar);
            }

            // Hapus data event
            $event->delete();

            return redirect()->back()->with('success', 'Event berhasil dihapus.');
            } catch(\Exception) {
                return redirect()->back()->with('error', 'Event gagal dihapus.');
            }
    }


    public function edit($id)
        {
            $event = Event::findOrFail($id);
            return view('events.edit', compact('event'));
        }

    public function update(Request $request)
        {            
            try{
                $event = Event::findOrFail($request->id);

            // $request->validate([
            //     'nama' => 'required|string|max:255',
            //     'deskripsi' => 'required|string',
            //     'jenis_peminatan' => 'required|string',
            //     'gambar' => 'nullable|image|max:2048',
            // ]);

            $event->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'narasumber' => $request->narasumber,
                'jenis_peminatan' => $request->jenis_peminatan,
                'Lokasi' => $request->Lokasi,
                'link' => $request->link,
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'wilayah_koordinator' => $request->wilayah_koordinator,
            ]);

         

            // Lalu di dalam method:
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($event->gambar && Storage::disk('public')->exists($event->gambar)) {
                    Storage::disk('public')->delete($event->gambar);
                }

                // Simpan gambar baru
                $path = $request->file('gambar')->store('event_images', 'public');

                // Update kolom gambar
                $event->update(['gambar' => $path]);
            }

                return redirect()->route('events.index.admin')->with('success', 'Event berhasil diperbarui.');
                } catch(\Exception){
                return redirect()->route('events.index.admin')->with('error', 'Event berhasil diperbarui.');
            }
        } 


        //=================================
        //==========REGISTER EVENT=========
        //=================================

        public function register($event_id){            
           try{
                $anggota = Auth::user()->anggota;
                $anggota->eventsJoined()->syncWithoutDetaching([$event_id]);
                // return redirect()->back()->with('success', "Anda telah terdaftar pada event ini");
                return redirect()->route('events.show', $event_id)->with('success', "Anda telah terdaftar pada event ini");
           }catch(\Exception){
                // return redirect()->back()->with('error', "Terjadi kesalahan");
                return redirect()->route('events.show', $event_id)->with('error', "Terjadi kesalahan");
           }            
        }



        public function referralCheck($event_id)
        {
            return view('events.referral_check', compact('event_id'));
        }

     public function referralSubmit(Request $request, $event_id)
{
    $anggota = Auth::user()->anggota;

    try {
        // Jika user mengisi referral, simpan di pivot table
        if ($request->referral_code) {
            $anggota->eventsJoined()->syncWithoutDetaching([
                $event_id => ['referred_by' => $request->referral_code]
            ]);
        } else {
            // Jika tidak isi referral, tetap daftar event tanpa referral
            $anggota->eventsJoined()->syncWithoutDetaching([$event_id]);
        }

        return redirect()
            ->route('events.show', $event_id)
            ->with('success', "Anda telah terdaftar pada event ini.");

    } catch (\Exception $e) {

        return redirect()
            ->route('events.show', $event_id)
            ->with('error', "Terjadi kesalahan saat mendaftar event.");
    }
}

public function referralSkip($event_id)
{
    $anggota = Auth::user()->anggota;

    try {
        $anggota->eventsJoined()->syncWithoutDetaching([$event_id]);

        return redirect()
            ->route('events.show', $event_id)
            ->with('success', "Anda telah terdaftar pada event ini.");
    } catch (\Exception $e) {
        return redirect()
            ->route('events.show', $event_id)
            ->with('error', "Terjadi kesalahan saat mendaftar event.");
    }
}

public function alreadyJoined($event_id)
{
    return view('events.already_joined', compact('event_id'));
}








        //===========REGISTER DARI LUAR =========
        public function register2($event_id){
            return redirect()->route('login');
        }

        public function batalkan($event_id){
            try{
                $anggota = Auth::user()->anggota;
                $anggota->eventsJoined()->detach([$event_id]);
                return redirect()->back()->with('success', "Berhasil Membatalkan Pendaftaran");
           }catch(\Exception){
                return redirect()->back()->with('error', "Terjadi kesalahan");
           }            
        }

        //=======DAFTAR EVENT YANG TELAH DIDAFTAR======


        public function eventAktif(){
            $user = Auth::user();
            $anggota = $user->anggota;
            $events = $anggota->eventsJoined;
            
            return view('member.eventTerdaftar', compact('events'));
        }
}
