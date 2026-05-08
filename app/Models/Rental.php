<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    protected $fillable = [
        'ps_id', 'kasir_id', 'nama_pelanggan', 'mode_billing',
        'jam_mulai', 'jam_selesai', 'durasi_awal', 'durasi_tambahan',
        'harga_sewa', 'status',
    ];

    protected $casts = [
        'jam_mulai'   => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    public function psUnit()
    {
        return $this->belongsTo(PsUnit::class, 'ps_id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}