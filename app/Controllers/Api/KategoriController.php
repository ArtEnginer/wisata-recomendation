<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\KategoriModel;

class KategoriController extends BaseApi
{
    protected $modelName = KategoriModel::class;

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
