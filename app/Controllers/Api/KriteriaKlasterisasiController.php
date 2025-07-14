<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\KriteriaKlasterisasiModel;

class KriteriaKlasterisasiController extends BaseApi
{
    protected $modelName = KriteriaKlasterisasiModel::class;
    public function validateCreate(&$request)

    {
        return $this->validate([
            'nama' => 'required',
            'kode' => 'required|is_unique[kriteria_klasterisasi.kode]',
        ]);
    }
}
