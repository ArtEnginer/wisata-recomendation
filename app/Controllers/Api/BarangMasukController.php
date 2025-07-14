<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;

class BarangMasukController extends BaseApi
{
    protected $modelName = BarangMasukModel::class;
    protected $load = ['barang'];

    public function validateCreate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',
            'tanggal' => 'required|date',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }

    public function validateUpdate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',
            'tanggal' => 'required|date',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }


    public function beforeCreate(&$data)
    {
        $data['barang_kode'] = $this->request->getPost('barang_kode');
        $data['jumlah'] = $this->request->getPost('jumlah');
        $data['tanggal'] = $this->request->getPost('tanggal');
        $data['keterangan'] = $this->request->getPost('keterangan');

        $barang = BarangModel::where('kode', $data['barang_kode'])->first();

        if ($barang) {
            $barang->fill([
                'stok' => $barang->stok + $data['jumlah'],
            ]);
            $barang->save();
        } else {
            return $this->failNotFound('Barang not found');
        }
    }

    public function beforeDelete(&$data)
    {
        $barangMasuk = BarangMasukModel::find($data['id']);
        if ($barangMasuk) {
            $barang = BarangModel::where('kode', $barangMasuk->barang_kode)->first();
            if ($barang) {
                $barang->fill([
                    'stok' => $barang->stok - $barangMasuk->jumlah,
                ]);
                $barang->save();
            }
        } else {
            return $this->failNotFound('Barang Masuk not found');
        }
    }
}
