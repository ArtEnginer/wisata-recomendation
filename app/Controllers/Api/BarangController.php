<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangModel;

class BarangController extends BaseApi
{
    protected $modelName = BarangModel::class;
    protected $load = ['kategori'];

    public function validateCreate(&$request)
    {
        $rules = [
            'kode' => 'required',
            'nama' => 'required',
        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }

    public function validateUpdate(&$request)
    {
        $rules = [
            'kode' => 'required',
            'nama' => 'required',
        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }
}
