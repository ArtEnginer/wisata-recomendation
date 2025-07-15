<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WisataModel extends Model
{
    protected $table = 'wisata';
    protected $fillable = [
        "id",
        "kode",
        "nama",
        "alamat",
        "deskripsi",
        "gambar",
        "latitude",
        "longitude",
        "klaster",
    ];

    public function nilaiKriteriaKlasterisasi()
    {
        return $this->hasMany(NilaiKriteriaKlasterisasiModel::class, 'wisata_kode', 'kode');
    }
}
