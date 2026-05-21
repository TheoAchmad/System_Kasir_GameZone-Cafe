<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cabang;

class PsUnit extends Model
{
    protected $table = 'ps_units';

    protected $fillable = ['nomor_ps', 'tipe_ps', 'status', 'harga_per_jam'];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'ps_id');
    }

    public function activeRental()
    {
        return $this->hasOne(Rental::class, 'ps_id')->where('status', 'berjalan');
    }

    public function cabang()
{
    return $this->belongsTo(Cabang::class);
}
}