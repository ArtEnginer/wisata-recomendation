<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanModel extends Model
{
    protected $table = 'peminjaman';
    protected $fillable = [
        "id",
        'user_id',
        "barang_kode",
        "jumlah",
        "deskripsi",
        "tanggal_pinjam",
        "tanggal_kembali",
        "status_approval",
        "status",
    ];

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_kode', 'kode');
    }

    public function user()
    {
        return $this->belongsTo(PenggunaModel::class, 'user_id', 'id');
    }
}
