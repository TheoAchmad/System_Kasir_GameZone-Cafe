<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'transaksi_id','rental_id','menu_id','qty','harga','subtotal',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}