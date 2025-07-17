<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKriteriaPerengkinganModel extends Model
{
    protected $table = 'nilai_kriteria_perengkingan';
    protected $fillable = [
        "id",
        "kriteria_perengkingan_kode",
        "wisata_kode",
        "nilai",
    ];

    public function kriteriaPerengkingan()
    {
        return $this->belongsTo(KriteriaPerengkinganModel::class, 'kriteria_perengkingan_kode', 'kode');
    }

    public function wisata()
    {
        return $this->belongsTo(WisataModel::class, 'wisata_kode', 'kode');
    }
}
