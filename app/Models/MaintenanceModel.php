<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceModel extends Model
{
    protected $table = 'maintenance';
    protected $fillable = [
        "id",
        'user_id',
        "barang_kode",
        "jumlah",
        "deskripsi",
        "tanggal_pengajuan",
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
