<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $event = request('event');
        return view('auth.login', compact('event'));
    }

    /**
     * Handle an incoming authentication request.
     */
//    public function store(LoginRequest $request)
// {
   
//     $request->authenticate();
//     $request->session()->regenerate();

//     if($request->event){
//         try{
//             $anggota = Auth::user()->anggota;
//             $anggota->eventsJoined()->syncWithoutDetaching([$request->event]);
//             // return redirect()->back()->with('success', "Anda telah terdaftar pada event ini");
//             return redirect()->route('events.show', $request->event)->with('success', "Anda telah terdaftar pada event ini");
//         }catch(\Exception){
//             // return redirect()->back()->with('error', "Terjadi kesalahan");
//             return redirect()->route('events.show', $request->event)->with('error', "Terjadi kesalahan");
//         }          
//     }
//     return redirect()->route('dashboard');
    
// }

public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();

    // Jika ada event
    if ($request->event) {

        $anggota = Auth::user()->anggota;

        // Cek apakah sudah join event
        $alreadyJoined = $anggota->eventsJoined()
            ->where('events.id', $request->event)
            ->exists();

        if ($alreadyJoined) {
            // Redirect ke halaman popup "Sudah terdaftar"
            return redirect()->route('events.already.joined', $request->event);
        }

        // Jika belum → minta referral
        return redirect()->route('events.referral.check', $request->event);
    }

    return redirect()->route('dashboard');
}




    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login'); 
    }
}
