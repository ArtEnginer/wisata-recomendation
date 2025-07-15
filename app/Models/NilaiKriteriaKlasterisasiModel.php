<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKriteriaKlasterisasiModel extends Model
{
    protected $table = 'nilai_kriteria_klasterisasi';
    protected $fillable = [
        "id",
        "kriteria_klasterisasi_kode",
        "wisata_kode",
        "nilai",
    ];

    public function kriteriaKlasterisasi()
    {
        return $this->belongsTo(KriteriaKlasterisasiModel::class, 'kriteria_klasterisasi_kode', 'kode');
    }

    public function wisata()
    {
        return $this->belongsTo(WisataModel::class, 'wisata_kode', 'kode');
    }
}
