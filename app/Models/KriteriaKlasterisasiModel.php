<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaKlasterisasiModel extends Model
{
    protected $table = 'kriteria_klasterisasi';
    protected $fillable = [
        "id",
        "kode",
        "nama",
    ];
}
