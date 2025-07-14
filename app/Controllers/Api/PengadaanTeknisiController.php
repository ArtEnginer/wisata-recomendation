<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\BarangModel;
use App\Models\PengadaanTeknisiModel;

class PengadaanTeknisiController extends BaseApi
{
    protected $modelName = PengadaanTeknisiModel::class;
    protected $load = ['barang', 'user'];

    public function validateCreate(&$request)
    {
        $rules = [
            'barang_kode' => 'required',
            'jumlah' => 'required|integer',
            'alasan' => 'required',
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

        ];

        if (!$this->validate($rules)) {
            return false;
        }

        return true;
    }

    public function beforeCreate(&$data)
    {
        $data['user_id'] = auth()->user()->id;
    }

    public function acc($id)
    {

        $pengadaan_teknisi = PengadaanTeknisiModel::find($id);
        if (!$pengadaan_teknisi) {
            return $this->failNotFound('pengadaan_teknisi not found');
        }

        $pengadaan_teknisi->status = 'acc';
        $pengadaan_teknisi->save();


        $barang = $pengadaan_teknisi->barang;
        if ($barang) {
            $barang->stok += $pengadaan_teknisi->jumlah;
            $barang->save();
            $pengadaan_teknisi->tanggal_persetujuan = date('Y-m-d H:i:s');
            $pengadaan_teknisi->save();
        } else {
            return $this->failNotFound('Barang not found');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'pengadaan_teknisi approved successfully',
            'data' => $pengadaan_teknisi,
        ]);
    }
    public function tolak($id)
    {
        $pengadaan_teknisi = PengadaanTeknisiModel::find($id);
        if (!$pengadaan_teknisi) {
            return $this->failNotFound('pengadaan_teknisi not found');
        }

        $pengadaan_teknisi->status = 'tolak';
        $pengadaan_teknisi->save();

        return $this->respond([
            'status' => 'success',
            'message' => 'pengadaan_teknisi rejected successfully',
            'data' => $pengadaan_teknisi,
        ]);
    }

    public function beforeDelete(&$data)
    {
        $pengadaan_teknisi = PengadaanTeknisiModel::find($data['id']);
        if ($pengadaan_teknisi) {
            $barang = BarangModel::where('kode', $pengadaan_teknisi->barang_kode)->first();
            if ($barang) {
                $barang->fill([
                    'stok' => $barang->stok - $pengadaan_teknisi->jumlah,
                ]);
                $barang->save();
            } else {
                return $this->failNotFound('Barang not found');
            }
        } else {
            return $this->failNotFound('pengadaan_teknisi not found');
        }
    }
}
