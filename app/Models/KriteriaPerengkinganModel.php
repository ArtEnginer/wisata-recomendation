<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaPerengkinganModel extends Model
{
    protected $table = 'kriteria_perengkingan';
    protected $fillable = [
        "id",
        "kode",
        "nama",
    ];
}
