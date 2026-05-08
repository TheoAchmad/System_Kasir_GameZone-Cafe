<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $fillable = ['nama_cabang', 'alamat', 'is_aktif'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function psUnits()
    {
        return $this->hasMany(PsUnit::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}