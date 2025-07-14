<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    protected $table = 'kategori';
    protected $fillable = [
        "id",
        "nama",
        "kode",
        "created_at",
        "updated_at",
    ];
}
