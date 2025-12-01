<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\crmController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/login', function () {
    return view('auth/login');
});

// Route::get('/dashboard', function () {
//     return view('member.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    // Route::get('/dashboard', [DashboardController::class, 'index'])
    //     ->middleware('role:admin') // Untuk Admin
    //     ->name('admin.dashboard');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
    Route::get('/member-dashboard', [DashboardController::class, 'memberDashboard'])
        ->middleware('role:member') // Untuk Member
        ->name('member.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //anggota crud
    Route::get('member_list', [AnggotaController::class, 'index'])->name('memberlist');
    Route::get('member_create', [AnggotaController::class, 'create'])->name('member.create');
    Route::post('member_create', [AnggotaController::class, 'store'])->name('member.create');
    Route::get('member_edit/{id_user}', [AnggotaController::class, 'edit'])->name('member.edit');
    Route::post('member_update', [AnggotaController::class, 'update'])->name('member.update');
    Route::get('member_delete/{id_user}', [AnggotaController::class, 'delete'])->name('member.delete');

    //profile 
    Route::get('/profile', [AnggotaController::class, 'profile'])->name('member.profile');
    Route::post('/profile_update', [AnggotaController::class, 'profile_update'])->name('member.profile.update');

    //event
    Route::get('events', [EventsController::class, 'index'])->name('event.list');
    Route::get('/events_admin', [EventsController::class, 'index_admin'])->name('events.index.admin');
    Route::get('/events_create', [EventsController::class, 'create'])->name('events.create');    
    Route::post('/admin/events', [EventsController::class, 'store'])->name('events.store');    
    Route::get('/admin/events/{id}', [EventsController::class, 'show'])->name('events.show');
    Route::get('/admin/events/{id}/edit', [EventsController::class, 'edit'])->name('events.edit');
    Route::post('/admin/events/edit', [EventsController::class, 'update'])->name('events.update');
    Route::get('/admin/events/{id}/delete', [EventsController::class, 'delete'])->name('events.delete');

    Route::get('/events/registered/active', [EventsController::class, 'eventAktif'])->name('events.registered.aktif');

    // route referral
    // Tampilan referral form setelah login
    Route::get('/events/{id_event}/referral-check', [EventsController::class, 'referralCheck'])
        ->name('events.referral.check');

    Route::get('/events/{event}/referral-skip', [EventsController::class, 'referralSkip'])
    ->name('events.referral.skip');

    Route::get('/events/{event}/already-joined', [EventController::class, 'alreadyJoined'])
    ->name('events.already.joined');

    Route::post('/events/{id_event}/referral-submit', [EventsController::class, 'referralSubmit'])
        ->name('events.referral.submit');

    //event register
    Route::get('/events/register/{id_event}', [EventsController::class, 'register'])->name('events.register');

    Route::get('/events/register/batalkan/{id_event}', [EventsController::class, 'batalkan'])->name('events.register.batalkan');

    //notifikasi 
    Route::get('mail/notification/{id_event}', [EmailController::class, 'send_notification'])->name('emails.notification');
    // Route::get('mail/notification/broadcast/{id_event}', [EmailController::class, 'broadcast'])->name('emails.notification.broadcast');
    Route::get('/broadcast/email/{id_event}', [EmailController::class, 'broadcast'])->name('broadcast.email');
    Route::post('/broadcast/email/send', [EmailController::class, 'broadcastSend'])->name('broadcast.send');



    //request koordinator
    Route::get('/request', [AnggotaController::class, 'index_request'])->name('member.request');
    Route::post('/request-koordinator', [AnggotaController::class, 'requestKoordinator'])
    ->name('request.koordinator')
    ->middleware('auth');

    //request koordinator - ADMIN
    Route::get('/admin/koordinator-list', [AnggotaController::class, 'koordinator_list'])->name('koordinatorlist');

    Route::get('/admin/koordinator-requests',  [AnggotaController::class, 'listRequests'])
        ->name('admin.koordinator.requests');

    Route::post('/admin/koordinator-requests/{id}/approve', [AnggotaController::class, 'approve'])
        ->name('admin.koordinator.approve');

    Route::post('/admin/koordinator-requests/{id}/reject', [AnggotaController::class, 'reject'])
        ->name('admin.koordinator.reject');



    
    //=======LAYANAN CUSTOM EMAIL BROADCASTING =======//
    ////////////////////
    //tampilin dashboard awal email 
    Route::get('mail', [EmailController::class, 'index'])->name('emails.index');    
    //nampilin form 
    Route::get('mail/create', [EmailController::class, 'create_email'])->name('emails.create');
    //menyimpan draft email sebelum dikirim
    Route::post('mail/create/store', [EmailController::class, 'store_email'])->name('emails.store');
    //nampilin halaman buat milih penerima
    Route::get('mail/create/penerima/{email_id}', [EmailController::class, 'list_penerima'])->name('emails.penerima');
    //nampilin form edit email, untuk edit email, untuk tombol back
    Route::get('mail/edit/{email_id}', [EmailController::class, 'edit_email'])->name('emails.edit');
    //simpan perubahan email
    Route::post('mail/update/', [EmailController::class, 'update_email'])->name('emails.update');
    //kirim email ke penerima 
    Route::post('mail/send', [EmailController::class, 'send_email'])->name('emails.send');
    //hapus riwayat email
    Route::get('mail/delete/{email_id}', [EmailController::class, 'delete_email'])->name('emails.delete');

    
    // ===================CRM BROADCASTING=======================
    Route::get('/crm', [crmController::class, 'index'])->name('crm.index');
    Route::get('crm/create', [crmController::class, 'create'])->name('crm.create');
    Route::post('crm/store', [crmController::class, 'store'])->name('crm.store');
    Route::get('crm/edit/{id}', [crmController::class, 'edit'])->name('crm.edit');
    Route::post('crm/update', [crmController::class, 'update'])->name('crm.update');
    Route::get('crm/destroy/{id}', [crmController::class, 'destroy'])->name('crm.destroy');
    Route::get('crm/write', [crmController::class, 'write'])->name('crm.write');
    Route::get('crm/store_mail', [crmController::class, 'store_mail'])->name('crm.store_mail');
    Route::get('crm/recipients/{id}', [crmController::class, 'recipients'])->name('crm.recipients');

});

//SHOW EVENT UNTUK DILUAR ADMIN
    Route::get('/admin/events/show/{id}', [EventsController::class, 'show2'])->name('events.show2');
    Route::post('/register2', [RegisteredUserController::class, 'register2'])->name('register2');
    Route::get('/test2', [RegisteredUserController::class, 'test2'])->name('test2');

require __DIR__.'/auth.php';
