<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanKaryawanModel extends Model
{
    protected $table = 'permintaan_perlengkapan';
    protected $fillable = [
        "id",
        'user_id',
        "barang_kode",
        "jumlah",
        "status",
        "alasan",
        "tanggal_pengajuan",
        "tanggal_persetujuan",
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
