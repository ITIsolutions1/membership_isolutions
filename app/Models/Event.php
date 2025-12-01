<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Anggota;

class Event extends Model
{
    use HasFactory;
    public $guarded = [];
    public function koordinator(){
        return $this->belongsTo(Anggota::class);
    }

    // public function peserta(){
    //     return $this->belongsToMany(Anggota::class, 'anggota_events');
    // }

public function anggotaJoined(){
     return $this->belongsToMany(Anggota::class, 'anggota_events', 'events_id', 'anggota_id')
                 ->withPivot('referred_by'); 
}

}
