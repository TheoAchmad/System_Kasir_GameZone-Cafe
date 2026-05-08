<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'rental_id','kasir_id','tipe_transaksi',
        'subtotal_sewa','subtotal_menu','diskon',
        'total_bayar','uang_bayar','kembalian',
        'metode_bayar','tanggal','status_bayar',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}