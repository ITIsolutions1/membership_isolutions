<?php

namespace App\Http\Controllers;

use App\Mail\BroadcastEmail;
use App\Mail\BroadcastNotifikasi;
use App\Mail\SingleMail;
use App\Mail\notifikasi;
use App\Mail\daftarDanRegister;
use App\Models\Anggota;
use App\Models\EmailHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Peminatan;
use App\Models\Event;

class EmailController extends Controller
{

    public function index(Request $request)
        {
            $user = Auth::user();
            $emails = $user->email_histories;
            $emails_today = $user->email_histories()->whereDate('created_at', today())->get();
            $emails_this_month = $user->email_histories()->whereMonth('created_at', today()->month)
                                                        ->whereYear('created_at', today()->year)
                                                        ->get();
            return view('emails.index', compact('emails', 'emails_today', 'emails_this_month'));
            // // Ambil semua peminatan (untuk filter dropdown)
            // $peminatans = Peminatan::all();

            // // Mulai query anggota
            // $query = Anggota::query();

            // // Filter berdasarkan peminatan (many-to-many)
            // if ($request->filled('peminatan_id')) {
            //     $query->whereHas('peminatan', function ($q) use ($request) {
            //         $q->where('peminatan_id', $request->peminatan_id);
            //     });
            // }

            // // Search berdasarkan nama atau email
            // if ($request->filled('search')) {
            //     $search = $request->search;
            //     $query->where(function ($q) use ($search) {
            //         $q->where('nama', 'like', "%$search%")
            //         ->orWhere('email', 'like', "%$search%");
            //     });
            // }

            // // Ambil hasil akhir
            // $anggota = $query->with('peminatan')->get();
            // return view('emails.index', compact('anggota', 'peminatans'));
        }

    /////////////////////////////
    //=====NOTIFICATION=========
    ////////////////////////////

     //UNTUK DAFTAR DAN REGISTER , PENDAFTAR DI LUAR LOGIN
    public function daftarDanRegister()
    {

    }

public function send_notification(Request $request, $id_event)
{
    try {

        $event = Event::where('id', $id_event)->first();

        $anggota = $event->anggotaJoined;

        $pesan = $request->pesan;

        foreach($anggota as $item){

            Mail::to($item->email)->send(
                new notifikasi(
                    $event,
                    $item->nama,
                    $pesan
                )
            );

        }

        return back()->with('success', 'Email notifikasi berhasil dikirim.');

    } catch(\Exception $e){

        return $e->getMessage();

    }
}

    public function broadcast(Request $request, $id_event)
    {
        $event = Event::findOrFail($id_event);
        $peminatans = Peminatan::all();

        $query = Anggota::query();

        if ($request->filled('peminatan_id')) {
            $query->whereHas('peminatan', function ($q) use ($request) {
                $q->where('peminatan_id', $request->peminatan_id);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $currentSort = $request->get('sort', 'nama');
        $currentDirection = $request->get('direction', 'asc');

        if (in_array($currentSort, ['nama', 'email', 'level', 'tanggal']) && in_array($currentDirection, ['asc', 'desc'])) {
            $query->orderBy($currentSort, $currentDirection);
        }

        $anggota = $query->with('peminatan')->get();

        return view('emails.broadcast', compact('anggota', 'peminatans', 'event', 'currentSort', 'currentDirection'));
    }

    // Fungsi kirim broadcast untuk yang ada di halaman event
    // Fungsi untuk mengirim email
   public function broadcastsend(Request $request)
    {
       try{
         $request->validate([
            'event_id' => 'required|exists:events,id',
            'emails'   => 'required|array|min:1',
            'emails.*' => 'integer'
        ]);

        $ids = $request->emails;
        $anggota = Anggota::whereIn('id', $ids)->get();
        $event = Event::findOrFail($request->event_id);

        foreach ($anggota as $item) {
            Mail::to($item->email)->queue(new BroadcastNotifikasi($event, $item->nama));
        }

        // return back()->with('success', 'Broadcast email berhasil dikirim!');
        return redirect()->route('events.index.admin')->with('success', 'Berhasil mengirimkan broadcast notifikasi');
       }catch(\Exception){
        return redirect()->route('events.index.admin')->with('error', 'Terjadi Kesalahan');
       }
    }



    // public function sendSingleEmail(Request $request)
    //     {

    //         $message = 'ini pesan test dulu';
    //         Mail::to($request->email)->queue(new SingleMail($message));
    //         return back()->with('success', 'Email berhasil dikirim ke semua user!');

    //         // try{
    //         //     $message = 'ini pesan test dulu';
    //         //     Mail::to($request->email)->queue(new SingleMail($message));
    //         //     return back()->with('success', 'Email berhasil dikirim ke semua user!');
    //         // } catch(\Exception) {
    //         //     return back()->with('error', 'Terjadi Kesalahan !');
    //         // }
    //         // $message = $request->get('message');

    //     }

    // public function sendBroadcast(Request $request)
    //     {
    //         $users = User::all(); // atau filter tertentu
    //         $message = $request->get('message');

    //         foreach ($users as $user) {
    //             Mail::to($user->email)->queue(new BroadcastEmail($message));
    //         }

    //         return back()->with('success', 'Email berhasil dikirim ke semua user!');
    //     }


    // public function preview(Request $request)
    // {
    //     $events = Event::latest()->get();

    //     $selectedEvent = null;
    //     if ($request->has('event_id')) {
    //         $selectedEvent = Event::find($request->event_id);
    //     }

    //     return view('broadcast.preview', compact('events', 'selectedEvent'));
    // }

    //====================================================
    //BUAT CUSTOM EMAIL DAN KIRIM EMAIL ==================
    // ===================================================
    //nampilin form
    public function create_email(){
        return view('emails.create');
    }
    //simpen draft
    public function store_email(Request $request){
        $email = (object)[
            'subject' => $request->subject,
            'body' => $request->body
        ];
        if($request->picture){
            $picturePath = $request->file('picture')->store('email_images', 'public');
        }else {
            $picturePath = null;
        }
        $email = EmailHistory::create([
            'sent_by' => Auth::user()->id,
            'subject' => $email->subject,
            'body' => $email->body,
            'status' => 'Pending',
            'image_url' => $picturePath
        ]);
        return redirect()->route('emails.penerima', $email->id);
    }

    // buka form untuk edit email
    public function edit_email($email_id){
        $email = EmailHistory::findOrFail($email_id);
        return view('emails.edit', compact('email'));
    }
    //kirim hasil perubahan email ke database
    public function update_email(Request $request){
        $email = EmailHistory::findOrFail($request->email_id);
        $email->update([
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => 'Pending',
        ]);

        if($request->hasFile('picture')){
            if($email->image_url && Storage::disk('public')->exists($email->image_url)){
                Storage::disk('public')->delete($email->image_url);
            }
            $picturePath = $request->file('picture')->store('email_images', 'public');
            $email->update([
                'image_url' => $picturePath
            ]);
        }

        return redirect()->route('emails.penerima', $request->email_id);
    }

    //milih penerima
    // public function list_penerima($email_id){
    //     $anggota = Anggota::all();
    //     return view('emails.penerima', compact('anggota', 'email_id'));
    // }

    public function list_penerima(Request $request, $email_id)
    {
        $query = Anggota::query();

        // Filter peminatan
        if ($request->filled('peminatan_id')) {
            $query->whereHas('peminatan', function ($q) use ($request) {
                $q->where('peminatan_id', $request->peminatan_id);
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Pencarian nama/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort')) {
            $direction = $request->get('direction', 'asc');
            $query->orderBy($request->sort, $direction);
        }


        $anggota = $query->get();
        $peminatans = Peminatan::all(); // Kirim untuk dropdown
        $email = EmailHistory::findOrFail($email_id);

        return view('emails.penerima', compact('anggota', 'email_id', 'peminatans'));
    }


    //kirim email broadcast yang ada di layanan email
    public function send_email(Request $request){
        $ids = $request->emails;
        $anggota = Anggota::whereIn('id', $ids)->get();

        $email_content = EmailHistory::findOrFail($request->email_id);
        foreach($anggota as $item){
            Mail::to($item->email)->queue(new BroadcastEmail($email_content, $item->nama));
        }
        $email_content->update([
            'status' => 'Terkirim'
        ]);

        return redirect()->route('emails.index')->with('success', 'email berhasil terkirim');
    }

    //fungsi untuk menghapus riwayat email
    public function delete_email($email_id){
        try{
            $email = EmailHistory::findOrFail($email_id);
            $email->delete();
            return back()->with('success', 'Email dihapus!');
        }catch(\Exception){
            return back()->with('error', 'Terjadi Kesalahan');
        }
    }



}
