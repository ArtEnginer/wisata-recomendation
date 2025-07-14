<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $fillable = [
        "id",
        "nama",
        "kode",
        "kategori_kode",
        "stok",
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_kode', 'kode');
    }
}
