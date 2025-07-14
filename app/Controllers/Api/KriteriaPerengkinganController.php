<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\KriteriaPerengkinganModel;

class KriteriaPerengkinganController extends BaseApi
{
    protected $modelName = KriteriaPerengkinganModel::class;
    public function validateCreate(&$request)

    {
        return $this->validate([
            'nama' => 'required',
            'kode' => 'required|is_unique[kriteria_klasterisasi.kode]',
        ]);
    }
}
