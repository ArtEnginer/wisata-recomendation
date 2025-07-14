<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangKeluarModel;
use App\Models\BarangModel;

class BarangKeluarController extends BaseApi
{
    protected $modelName = BarangKeluarModel::class;
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
                'stok' => $barang->stok - $data['jumlah'],
            ]);
            $barang->save();
        } else {
            return $this->failNotFound('Barang not found');
        }
    }

    public function beforeDelete(&$data)
    {
        $barangKeluar = BarangKeluarModel::find($data['id']);
        if ($barangKeluar) {
            $barang = BarangModel::where('kode', $barangKeluar->barang_kode)->first();
            if ($barang) {
                $barang->fill([
                    'stok' => $barang->stok + $barangKeluar->jumlah,
                ]);
                $barang->save();
            }
        } else {
            return $this->failNotFound('Barang Keluar not found');
        }
    }
}
