<?php
// ── Menu ──────────────────────────────────────
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = ['nama_menu','kategori','harga','stok','gambar','is_aktif'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}