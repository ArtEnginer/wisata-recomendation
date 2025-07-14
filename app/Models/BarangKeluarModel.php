<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluarModel extends Model
{
    protected $table = 'barang_keluar';
    protected $fillable = [
        "id",
        "barang_kode",
        "jumlah",
        "tanggal",
        "keterangan",
    ];

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_kode', 'kode');
    }
}
